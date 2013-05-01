<?php
/**
 * Created by JetBrains PhpStorm.
 * User: uqcmcna1
 * Date: 19/03/13
 * Time: 10:33 AM
 * To change this template use File | Settings | File Templates.
 */



function page_clinical_feature($clinical_feature, $bone_dysplasia=null) {
    // Get the Bone Dysplasias
    $bds = data_get_bone_dysplasias_for_clinical_feature($clinical_feature);

    // Get the Genes
    //$genes = data_get_genes_for_bone_dysplasias($bds);
    $genes = array();

    if(!count($bds)) {
        $information_content = 0;
    } else {
        /* Get the Information Content for this Bone Dysplasia */
        // see how many bone dysplasias have this clinical feature
        // and then work that out as a percentage of all bone dysplasia count

        $information_content = data_get_information_content_for_feature(
            count($bds),
            data_get_bone_dysplasia_count(),
            data_get_max_clinical_feature_count()
        );
    }

    drupal_add_js(array(
        'skeletome_builder' => array(
            "bone_dysplasia"            => $bone_dysplasia,
            "bone_dysplasias"           => $bds,
            'clinical_feature'          => $clinical_feature,
            'genes'                     => $genes,
            'information_content'       => $information_content
        )), 'setting');
}

function page_contact() {
    $output =  array(
        'sb_contact_us' => array(
            '#data'     => NULL,
            '#theme'    => 'sb_contact_us'
        )
    );
    return $output;
}

function page_source_release($term) {

    // Get the source for the release
    $source = taxonomy_term_load($term->sk_gsr_field_group_source[LANGUAGE_NONE][0]['taxonomy_term']->tid);

    // Get all the releases for the source
    $releases = data_get_releases_for_source($source);

    // Go through and find the release, that matches our selected one, and mark it as selected
    foreach($releases as &$release) {
        if($release->tid == $term->tid) {
            $release->selected = true;

            // Get the tags for that release and attach them
            $tags_full = data_get_tags_for_release_id($release->tid);
            $release->tags = $tags_full;

            // Add all the group names for the tags
            foreach($tags_full as &$tag) {
                // get the group just in case we need it for something
                $tag->sk_gt_field_group_name = taxonomy_term_load($tag->sk_gt_field_group_name[LANGUAGE_NONE][0]['tid']);
            }
        }
    }

    drupal_add_js(array(
        'skeletome_builder' => array(
            "source"        => $source,
            'releases'      => $releases
    )), 'setting');
}

function page_source($term) {
    /**
     * Source Page
     */

    /* Get all the Releases */
    $releases_full = data_get_releases_for_source($term);

    // For the most recent release, lets get all the groups in it
    /* For each release, get all the tags bone dysplasias */
    foreach($releases_full as &$release) {

        $tags_full = data_get_tags_for_release_id($release->tid);

        /* Attach the tags to the release*/
        $release->tags = $tags_full;

        foreach($tags_full as &$tag) {
            // get the group
            $tag->sk_gt_field_group_name = taxonomy_term_load($tag->sk_gt_field_group_name[LANGUAGE_NONE][0]['tid']);
        }
    }

    drupal_add_js(array(
        'skeletome_builder' => array(
            "releases"      => $releases_full
    )), 'setting');
}

function page_group_tag($term) {
    /* Get the members */
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'bone_dysplasia')
        ->propertyCondition('status', 1)
        ->fieldCondition('sk_bd_tags', 'tid', $term->tid, '=');
//            ->addMetaData('account', user_load(1)); // Run the query as user 1.
    $results = $query->execute();

    $bone_dysplasia_ids = array();
    foreach($results['node'] as $bd_id) {
        $bone_dysplasia_ids[] = $bd_id->nid;
    }
    $bds = node_load_multiple($bone_dysplasia_ids);
    $bds = array_merge($bds, array());


    /* Get Common Clinical Features */
    $common_clinical_features = array();
    $count = 0;
    foreach($bds as $bd) {
        // get out clinical features
        if(!isset($bd->field_skeletome_tags[LANGUAGE_NONE])) {
            continue;
        }

        $clinical_features = $bd->field_skeletome_tags[LANGUAGE_NONE];
        $clinical_features_ids = array();
        foreach($clinical_features as $clinical_feature) {
            $clinical_features_ids[] = $clinical_feature['tid'];
        }

        if($count == 0) {
            $common_clinical_features = array_merge($clinical_features_ids, $common_clinical_features);
        } else {
            $common_clinical_features = array_intersect($clinical_features_ids, $common_clinical_features);
        }

        $count++;
    }

    $common_clinical_features_full = array_merge(taxonomy_term_load_multiple($common_clinical_features), array());

    // Add in the information  content
    $total_bd_count = data_get_bone_dysplasia_count();
    $max_count_feature_appearance = data_get_max_clinical_feature_count();

    foreach($common_clinical_features_full as &$common_clinical_feature_full) {
        $bds_with_feature_count = count(data_get_bone_dysplasias_for_clinical_feature($common_clinical_feature_full, false));

        $common_clinical_feature_full->information_content = data_get_information_content_for_feature(
            $bds_with_feature_count,
            $total_bd_count,
            $max_count_feature_appearance
        );
    }
//        echo "<h2>List</h2>";
//        echo "<pre>";
//        print_r($common_clinical_features_full);
//        echo "</pre>";

    /* Get Genes / Gene Mutations */
    $genes = data_get_genes_for_bone_dysplasias($bds);

    /* Get the Tags */
    $source_release = array();
    if(isset($term->sk_gt_field_group_source_release[LANGUAGE_NONE][0])) {
        $source_release = taxonomy_term_load($term->sk_gt_field_group_source_release[LANGUAGE_NONE][0]['tid']);
    }
    $source = array();
    if(isset($source_release->sk_gsr_field_group_source[LANGUAGE_NONE][0])) {
        $source = taxonomy_term_load($source_release->sk_gsr_field_group_source[LANGUAGE_NONE][0]['tid']);
    }
    $group_name = array();
    if(isset($term->sk_gt_field_group_name[LANGUAGE_NONE][0])) {
        $group_name = taxonomy_term_load($term->sk_gt_field_group_name[LANGUAGE_NONE][0]['tid']);
    }

    drupal_add_js(array(
        'skeletome_builder' => array(
            "members"           => $bds,
            'clinical_features' => $common_clinical_features_full,
            'genes'             => $genes,
            'source_release'    => $source_release,
            'source'            => $source,
            'group_name'        => $group_name
    )), 'setting');
}

function page_gene($gene, $bone_dysplasia=null) {

    /*
    // Get all the Gene Mutations
    $gene_mutation_ids = array();
    foreach($gene->field_gene_gene_mutation[LANGUAGE_NONE] as $gene_mutation) {
        $gene_mutation_ids[] = $gene_mutation['target_id'];
    }
    // Get the Gene Mutations
    $gene_mutations = array_values(node_load_multiple($gene_mutation_ids));

    // Need to process out hte 'unspecific mutations'
    $various_mutations_bd_ids = array();
    for($i = count($gene_mutations)-1; $i >= 0; $i--) {

        $gene_mutation = $gene_mutations[$i];
        if(strpos($gene_mutation->title, "unspecified mutation") !== false) {

            // This is an unspecified mutation
            // Pull it out and save it elsewhere
            $bd_ids = array();
            if(isset($gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE])) {
                foreach($gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE] as $bd_id) {
                    $bd_ids[] = $bd_id['target_id'];
                }
            }

            $various_mutations_bd_ids = array_merge($various_mutations_bd_ids, $bd_ids);

            unset($gene_mutations[$i]);

        }
    }

    $gene->field_gene_gene_mutation = $gene_mutations;
    $gene->various_mutations_bone_dysplasias = array_merge(array(), node_load_multiple($various_mutations_bd_ids));

    foreach($gene->field_gene_gene_mutation as &$gene_mutation) {
        // Add in the Gene Mutation Type
        if(isset($gene_mutation->field_gm_mutation_type[LANGUAGE_NONE])) {
            // get the gene mutation type
            $gene_mutation->field_gm_mutation_type = node_load($gene_mutation->field_gm_mutation_type[LANGUAGE_NONE][0]['target_id']);
        }

        // Add in the Bone Dysplasias
        $bd_ids = array();
        if(isset($gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE])) {
            foreach($gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE] as $bd_id) {
                $bd_ids[] = $bd_id['target_id'];
            }
        }

        $gene_mutation->field_gene_mutation_bd = array_merge(array(), node_load_multiple($bd_ids));
    }
    */


    $statements = data_get_statements_for_node($gene);

    drupal_add_js(array(
        'skeletome_builder' => array(
            'gene'              => $gene,
            'statements'        => $statements,
            'bone_dysplasias'   => data_get_bone_dysplasias_for_gene($gene),
            'bone_dysplasia'    => $bone_dysplasia,
            'editors'           => data_get_editors_for_node($gene->nid),
    )), 'setting');

}

function data_get_editors_for_node($node_id) {
    $sql = "SELECT DISTINCT u.*
            FROM {users} u
            INNER JOIN {node_revision} r
            ON u.uid = r.uid
            WHERE r.nid = :nid
            ORDER BY r.vid DESC";

    $result = db_query($sql, array(
        ':nid' => $node_id
    ));
    $users = array();
    foreach ($result as $user) {
        $users[] = $user;
    }
    return $users;
}

function page_bone_dysplasia($node) {

    /* Left over code */
    $main_menu = array();

    /* Get the statements */
    $statements = data_get_statements_for_node($node);

    /* Get Clinical Features */
    $clinical_features = data_get_clinical_features_for_bone_dysplasia($node);

    $bone_dysplasia_count = data_get_bone_dysplasia_count();
    $max_clinical_feature_appearance = data_get_max_clinical_feature_count();

    $max_log = -log(1/$bone_dysplasia_count);
    $min_log = -log($max_clinical_feature_appearance/$bone_dysplasia_count);

    foreach($clinical_features as &$clinical_feature) {
        // get all the bone dysplasias
        $bone_dysplasias_with_feature = data_get_bone_dysplasias_for_clinical_feature($clinical_feature, false);

        $clinical_feature['information_content'] = data_get_information_content_for_feature(
            count($bone_dysplasias_with_feature),
            $bone_dysplasia_count,
            $max_clinical_feature_appearance
        );
    }

    /* Get OMIM */
    $omim = data_get_omim_for_bone_dysplasia($node);

    /* Get mode of inheritance */
    $moi = data_get_moi($node);



    /* Get the Genes */
    $genes = data_get_genes_for_bone_dysplasias(array($node));


    /* Get the Groups */
    $tags = data_get_groups_and_tags($node);

    /* Get all the modes of inhertiance (used by editing feature) */
    $moi_taxonomy = taxonomy_vocabulary_machine_name_load('mode_of_inheritance');

    $sql = "SELECT *
                FROM {taxonomy_term_data}
                WHERE vid = :vid";

    $mois_object = db_query($sql, array(
        'vid'       => $moi_taxonomy->vid,
    ));

    $all_mois = array();
    foreach($mois_object as $moi_object) {
        $all_mois[] = $moi_object;
    }

//    $all_genes = data_all_genes(1000);
    $all_genes = array();

    if(!isset($node->body[LANGUAGE_NONE])) {
        $node->body[LANGUAGE_NONE][0]['value'] = "";
    }

    // Get list of similar nodes
    $block = block_load("apachesolr_search", "mlt-001");
    $similar = _block_get_renderable_array(_block_render_blocks(array($block)));
    $similar_bone_dysplasias = null;
    if(isset($similar['apachesolr_search_mlt-001'])) {
        $similar_bone_dysplasias = $similar['apachesolr_search_mlt-001']['#docs'];
    }



    // setup the images
    if(isset($node->field_bd_xray_images[LANGUAGE_NONE])) {
        foreach($node->field_bd_xray_images[LANGUAGE_NONE] as &$image_test) {
            $image_test['full_url'] = file_create_url($image_test['uri']);
            $image_test['thumb_url'] = image_style_url('thumbnail', $image_test['uri']);
        }
        $node->field_bd_xray_images[LANGUAGE_NONE] = array_reverse($node->field_bd_xray_images[LANGUAGE_NONE]);
    } else {
        $node->field_bd_xray_images[LANGUAGE_NONE] = array();
    }



    // Setup the sub-types
    if(isset($node->field_bd_subbd[LANGUAGE_NONE])) {
        $bd_ids = array();
        foreach($node->field_bd_subbd[LANGUAGE_NONE] as $bd) {
            $bd_ids[] = $bd['target_id'];
        }
        $node->field_bd_subbd = array_values(node_load_multiple($bd_ids));
    }

    // Setup the super types
    if(isset($node->field_bd_superbd[LANGUAGE_NONE])) {
        $bd_ids = array();
        foreach($node->field_bd_superbd[LANGUAGE_NONE] as $bd) {
            $bd_ids[] = $bd['target_id'];
        }
        $node->field_bd_superbd = array_values(node_load_multiple($bd_ids));
    }

    if(isset($node->field_bd_sameas[LANGUAGE_NONE])) {
        $bd_ids = array();
        foreach($node->field_bd_sameas[LANGUAGE_NONE] as $bd) {
            $bd_ids[] = $bd['target_id'];
        }
        $node->field_bd_sameas = array_values(node_load_multiple($bd_ids));
    }


    // Get the descriptions
    $provider = null;
    $reference_string = null;

    if(isset($node->body[LANGUAGE_NONE][0]['safe_value'])) {
        $description = $node->body[LANGUAGE_NONE][0]['safe_value'];
        // Get the reference string
        $first_bracket_pos = strpos($description, '<p>[');
        $last_bracket_pos = strrpos($description, ']</p>') + 1;
        $length = 0;

        if($first_bracket_pos !== false && $last_bracket_pos !== false) {
            $length = $last_bracket_pos - $first_bracket_pos;

            $reference_string = substr($description, $first_bracket_pos, $length);

            // Clear the description
            $node->body[LANGUAGE_NONE][0]['safe_value'] = str_replace($reference_string, "", $description);
            // References
            $reference_string = str_replace("<p>[", "", $reference_string);
            $reference_string = str_replace("]</p>", "", $reference_string);
//            $reference_string = substr($reference_string, 1, strlen($reference_string) - 2);

            // Time to get out the 'available at' string


            if(strpos($description, 'GeneReviews') !== false) {
                $provider = "GeneReviews";
                // get out the position
            } else {
                $provider = "OMIM";
            }

        }
    }

    drupal_add_js(array(
        'skeletome_builder' => array(
            "bone_dysplasia"        => $node,
            'main_menu'             => $main_menu,
            'statements'            => $statements,
            'clinical_features'     => $clinical_features,
            'genes'                 => $genes,
            'omim'                  => $omim,
            'tags'                 => $tags,
            'moi'                   => $moi,
            'all_mois'              => $all_mois,
            'all_genes'             => $all_genes,
            'editors'               => data_get_editors_for_node($node->nid),
            'similar'               => $similar_bone_dysplasias,
            'reference'             => $reference_string,
            'provider'              => $provider
    )), 'setting');
}


function helper_pprint($array, $die=false) {
    echo "<pre>";
    print_r($array);
    echo "<pre>";
    if($die) {
        die();
    }
};