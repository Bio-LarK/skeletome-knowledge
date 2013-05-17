<?php
/**
 * Created by JetBrains PhpStorm.
 * User: uqcmcna1
 * Date: 19/03/13
 * Time: 9:45 AM
 * To change this template use File | Settings | File Templates.
 */


//function data_search_bone_dysplasias($term, $conditions_array, $index, $offset) {
//    // search for all bone dysplasias matching conditions
//    // get a count of that
//
//
//    //
//}

/**
 *
 * @param $term
 */
function data_autocomplete_search($term) {
    $limit = 5;

    // Get the Nodes
    $sql = "SELECT *
            FROM {node} n
            WHERE n.title LIKE :title
            AND n.type != 'gene_mutation'
            AND n.type != 'media_gallery'
            AND n.type != 'gene_mutation_type'
            AND n.type != 'statement'
            AND n.type != 'biblio'
            ORDER BY LENGTH(n.title) ASC
            LIMIT $limit";
    $results = db_query($sql, array(
        'title'     => db_like($term) .'%'
    ));

    $nodes = $results->fetchAll();

    // Now we need to add in the synonyms annoyingly
    $sql = "SELECT entity_id, field_bd_synonym_value
            FROM {field_data_field_bd_synonym} s
            WHERE s.field_bd_synonym_value LIKE :title
            ORDER BY LENGTH(field_bd_synonym_value) ASC
            LIMIT $limit";
    $results = db_query($sql, array(
        'title'     => db_like($term) .'%'
    ));

    $node_ids = array();
    $node_titles = array();
    foreach($results as $result) {
        $node_ids[] = $result->entity_id;
        $node_titles[] = $result->field_bd_synonym_value;
    }
    $bd_syns = array_values(node_load_multiple($node_ids));
    for($i = 0; $i < count($node_ids); $i++) {
        $bd_syn = $bd_syns[$i];
        $bd_syn->title = $node_titles[$i];
    }

    $vocab = taxonomy_vocabulary_machine_name_load('sk_group_name');
    $vocab_id = $vocab->vid;
    $sk_group_source_release = taxonomy_vocabulary_machine_name_load('sk_group_source_release');
    $sk_group_source_release_id = $sk_group_source_release->vid;

    // Get the terms
    $sql = "SELECT t.*, v.machine_name
            FROM {taxonomy_term_data} t
            LEFT JOIN {taxonomy_vocabulary} v
            ON v.vid = t.vid
            WHERE t.name LIKE :name
            AND t.vid != $vocab_id
            AND t.vid != $sk_group_source_release_id
            ORDER BY LENGTH(t.name) ASC
            LIMIT $limit";

    $results = db_query($sql, array(
        'name'      => db_like($term) . '%'
    ));
    $terms = array();
    foreach($results as $result) {
        $terms[] = $result;
    }

    $users = array();

    $results = array_merge($nodes, $bd_syns, $terms, $users);

    usort($results, "cmp_length");

    return $results;
}

function cmp_length($a, $b)
{
    if(isset($a->title)) {
        $a_title = $a->title;
    } else {
        $a_title = $a->name;
    }

    if(isset($b->title)) {
        $b_title = $b->title;
    } else {
        $b_title = $b->name;
    }

    // comapre titles
    if (strlen($a_title) == strlen($b_title)) {
        return 0;
    }
    return (strlen($a_title) > strlen($b_title)) ? 1 : -1;

}


function data_node_search($term, $full = true) {
    if(count($term < 3)) {
        $limit = 500;
    } else {
        $limit = 99999;
    }

    $sql = "SELECT n.nid
            FROM {node} n
            WHERE n.title LIKE :title
            AND n.type != 'media_gallery'
            AND n.type != 'gene_mutation_type'
            LIMIT $limit";

    $results = db_query($sql, array(
        'title'     => '%'. db_like($term) .'%'
    ));

    $node_ids = array();
    foreach($results as $result) {
        $node_ids[] = $result->nid;
    }

    if($full) {
        $nodes = node_load_multiple($node_ids);

        foreach($nodes as &$node) {

            // Remove the trash (published time etc, slows down page load)
            unset_node_trash($node);

            if($node->type == "bone_dysplasia") {
                $cfs_full = data_get_clinical_features_for_bone_dysplasia($node);
                $cfs = array();
                foreach($cfs_full as $cf_full) {
                    $cfs[] = array(
                        'tid'   => $cf_full['tid'],
                        'name'  => $cf_full['name']
                    );
                }
                $node->field_skeletome_tags = $cfs;

                $tags_full = data_get_tags_for_bone_dysplasia($node);
                $tags = array();
                foreach($tags_full as $tag_full) {
                    $tags[] = array(
                        'tid'   => $tag_full['tid'],
                        'name'  => $tag_full['name']
                    );
                }
                $node->sk_bd_tags = $tags;

                $genes_full = data_get_genes_for_bone_dysplasias(array($node));
//                $genes = array();
//                foreach($genes_full as $gene_full) {
//                    $genes[] = array(
//                        'nid'       => $gene_full['gene_mutation']->nid,
//                        'title'     => $gene_full['gene_mutation']->title,
//                        'gene'      => array(
//                            'nid'       => $gene_full['gene']->nid,
//                            'title'     => $gene_full['gene']->title
//                        )
//                    );
//                }

//                $node->field_bd_gm = $genes;
            }
        }

        return $nodes;



    } else {
        return $node_ids;
    }
}


function data_term_search($term, $full = true) {
    if(count($term < 3)) {
        $limit = 500;
    } else {
        $limit = 99999;
    }

    // We want to filter out the group names, they shouldnt be searched for
    $vocab = taxonomy_vocabulary_machine_name_load('sk_group_name');
    $vocab_id = $vocab->vid;

    $sql = "SELECT *
            FROM {taxonomy_term_data} t
            WHERE t.name LIKE :name
            AND t.vid != $vocab_id
            LIMIT $limit";

    $results = db_query($sql, array(
        'name'      => '%' . db_like($term) . '%'
    ));
    $term_ids = array();
    foreach($results as $result) {
        $term_ids[] = $result->tid;
    }


    if($full) {
        return taxonomy_term_load_multiple($term_ids);
    } else {
        return $term_ids;
    }
}


function unset_node_trash($elements, $keep_body = true) {

    if(!is_array($elements)) {
        $nodes = array($elements);
    } else {
        $nodes = $elements;
    }
    foreach($nodes as &$node) {
        unset($node->uid);
        unset($node->log);
        unset($node->status);
        unset($node->comment);
        unset($node->promote);
        unset($node->sticky);
        unset($node->language);
        unset($node->created);
        unset($node->changed);
        unset($node->tnid);
        unset($node->translate);
        unset($node->revision_timestamp);
        unset($node->revision_uid);
        unset($node->rdf_mapping);
        if(!$keep_body) {
            unset($node->body[LANGUAGE_NONE][0]['value']);
        }
        unset($node->body[LANGUAGE_NONE][0]['safe_value']);
        unset($node->last_comment_timestamp);
        unset($node->last_comment_name);
        unset($node->last_comment_uid);
        unset($node->comment_count);
        unset($node->data);
    }

    if(!is_array($elements)) {
        return $nodes[0];
    } else {
        return $nodes;
    }

}
/**
 * Get all the clinical features of a bone dysplasia
 * @param $bone_dysplasia
 * @return array
 */
function data_get_tags_for_bone_dysplasia($bone_dysplasia) {
    $tags_array = array();

    $field_sk_bd_tags = field_get_items('node', $bone_dysplasia, 'sk_bd_tags');

    $tag_ids = array();
    if($field_sk_bd_tags) {
        foreach($field_sk_bd_tags as $field_sk_bd_tag) {
            $tag_ids[] = $field_sk_bd_tag['tid'];
        }
    }

    $tags_objects = taxonomy_term_load_multiple($tag_ids);
    foreach($tags_objects as $tags) {
        $tags_array[] = (array)$tags;
    }
    return $tags_array;
}


/**
 * Get Bone Dysplasias for Genes
 * @param $gene
 * @param bool $full
 * @return array
 */
function data_get_bone_dysplasias_for_gene($gene, $full = true) {
    $gene = (array)$gene;

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'gene_mutation')
        ->propertyCondition('status', 1)
        ->fieldCondition('field_gene_mutation_gene', 'target_id', $gene['nid'], '=');
    $results = $query->execute();

    $gm_ids = get_ids($results, 'node');

    $bd_ids = array();
    foreach($gm_ids as $gm_id) {
        $query = new EntityFieldQuery();
        $query->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', 'bone_dysplasia')
            ->propertyCondition('status', 1)
            ->fieldCondition('field_bd_gm', 'target_id', $gm_id, '=');
        $results = $query->execute();
        $bd_ids = array_merge($bd_ids, get_ids($results, 'node'));
    }

    if($full) {
        $bds = node_load_multiple($bd_ids);
        $bds = array_merge($bds, array());
        return $bds;
    } else {
        return get_ids($results, 'node');
    }
}
/**
 * Get the information content for a feature
 * @param $bds_with_feature_count           The number of Bone Dysplasias with the feature
 * @param $total_bd_count                   The total number of bone dysplasias
 * @param $max_count_feature_appearance     The max count of feature occurence across all bone dysplasias
 * @return float
 */
function data_get_information_content_for_feature($bds_with_feature_count, $total_bd_count, $max_count_feature_appearance) {
    $max_log = -log(1/$total_bd_count);
    $min_log = -log($max_count_feature_appearance/$total_bd_count);

    return (-log($bds_with_feature_count/$total_bd_count) - $min_log) / ($max_log - $min_log) * 100;
}

function data_get_tags_for_release_id($release_id) {
    $taxonomyQuery = new EntityFieldQuery();
    $taxonomyTerms = $taxonomyQuery->entityCondition('entity_type', 'taxonomy_term')
        ->fieldCondition('sk_gt_field_group_source_release', 'tid', $release_id)
        ->execute();

    /* Get out all the group names */
    /* Dont really need hte tags */
    $tags_full = array();
    $tags_full = array_values(taxonomy_term_load_multiple(get_ids($taxonomyTerms, 'taxonomy_term')));

    return $tags_full;
}

/**
 * Get all the releases for a source object
 * @param $source
 * @return object    Releases for source
 */
function data_get_releases_for_source($source) {
    $taxonomyQuery = new EntityFieldQuery();
    $taxonomyTerms = $taxonomyQuery->entityCondition('entity_type', 'taxonomy_term')
        ->fieldCondition('sk_gsr_field_group_source', 'tid', $source->tid)
        ->execute();

    $releases_full = taxonomy_term_load_multiple(get_ids($taxonomyTerms, 'taxonomy_term'));

    usort($releases_full, "cmp");

    return $releases_full;
}

/**
 * Get the number of bone dysplasias in the system.
 *
 * @return integeter
 */
function data_get_bone_dysplasia_count() {
    return db_query("SELECT count(nid) FROM {node} WHERE node.type = 'bone_dysplasia'")->fetchField();
}

/**
 * Get the max count for a clinical feature occuring in a bone dyplasia
 *
 * For example, Short Stature has the most, with 127 bone dysplasias
 *
 * @return  count (e.g. 127)
 */
function data_get_max_clinical_feature_count() {
    $sql = "SELECT MAX(cnt) FROM (SELECT field_skeletome_tags_tid, COUNT(entity_id) as cnt
            FROM field_data_field_skeletome_tags
            GROUP BY field_skeletome_tags_tid) as list";
    $total_count = db_query($sql)->fetchField();
    return $total_count;
}

/**
 * Get the Bone Dysplasias for a Clinical Feature
 *
 * @param $clinical_feature
 * @return array
 */
function data_get_bone_dysplasias_for_clinical_feature($clinical_feature, $full=true) {

    $clinical_feature = (object)$clinical_feature;

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'bone_dysplasia')
        ->propertyCondition('status', 1)
        ->fieldCondition('field_skeletome_tags', 'tid', $clinical_feature->tid, '=');
//            ->addMetaData('account', user_load(1)); // Run the query as user 1.
    $results = $query->execute();


    if($full) {
        $bds = node_load_multiple(get_ids($results, 'node'));
        $bds = array_merge($bds, array());
        return $bds;
    } else {
        return get_ids($results, 'node');
    }
}

function data_get_gene_mutation_type_for_gene_mutation($gene_mutation) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'gene_mutation_type')
        ->propertyCondition('status', 1)
        ->fieldCondition('field_gm_mutation_type', 'target_id', $gene_mutation->nid, '=');
    $results = $query->execute();

    $ids = get_ids($results, 'node');
    if(count($ids) > 0)
        $gene_mutation_type = node_load($ids[0]);

    $gene_mutation_type = null;
    return $gene_mutation_type;

}
function data_get_gene_mutations_for_gene($gene) {
    if(!isset($gene->field_gene_gene_mutation)) {
        $gene = node_load($gene->nid);
    }

    // Get all the gene mutation ids
    $gene_mutation_ids = array();
    if(isset($gene->field_gene_gene_mutation[LANGUAGE_NONE][0])) {
        foreach($gene->field_gene_gene_mutation[LANGUAGE_NONE] as $gene_mutation) {
            $gene_mutation_ids[] = $gene_mutation['target_id'];
        }
    }

//    echo "<pre>";
//    print_r($gene_mutation_ids);
//    echo "</pre>";
//
//
//    die();
//    $query = new EntityFieldQuery();
//    $query->entityCondition('entity_type', 'node')
//        ->entityCondition('bundle', 'gene_mutation')
//        ->propertyCondition('status', 1)
//        ->fieldCondition('field_gene_mutation_gene', 'target_id', $gene->nid, '=');
//    $results = $query->execute();

    $gene_mutations = node_load_multiple($gene_mutation_ids);

    foreach($gene_mutations as &$gene_mutation) {
        $gene_mutation->gene_mutation_type = data_get_gene_mutation_type_for_gene_mutation($gene_mutation);
    }

    return array_values($gene_mutations);
}

function data_create_gene_mutation_for_gene($gene_id, $title) {

    // Create the Gene Mutation
    global $user;
    $gene_mutation = new stdClass();
    $gene_mutation->title = $title;
    $gene_mutation->type = "gene_mutation";
    node_object_prepare($gene_mutation); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
    $gene_mutation->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
    $gene_mutation->uid = $user->uid;
    $gene_mutation->status = 1; //(1 or 0): published or not
    $gene_mutation->promote = 0; //(1 or 0): promoted to front page
    $gene_mutation->comment = 1; //2 = comments on, 1 = comments off
    $gene_mutation = node_submit($gene_mutation); // Prepare node for saving
    $gene_mutation->field_gene_mutation_gene = array(
        LANGUAGE_NONE => array(
           array(
               'target_id' => $gene_id
           )
        )
    );
    node_save($gene_mutation);

    // Add to a gene
    $gene = node_load($gene_id);
    $gene->field_gene_gene_mutation[LANGUAGE_NONE][]['target_id'] = $gene_mutation->nid;
    node_save($gene);

    return $gene_mutation;
}


function data_create_statement_for_node($statement, $node_id) {
    global $user;
    $node = new stdClass();
    $node->title = "Statement";
    $node->type = "statement";
    node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
    $node->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
    $node->uid = $user->uid;
    $node->status = 1; //(1 or 0): published or not
    $node->promote = 0; //(1 or 0): promoted to front page
    $node->comment = 1; //2 = comments on, 1 = comments off

    $node->body[$node->language][0]['value'] = $statement; // the statement
    $node->body[$node->language][0]['format'] = 'filtered_html';

    $node->field_statement_node[$node->language][] = array(
        'target_id'     => $node_id
    );

    $node = node_submit($node); // Prepare node for saving
    node_save($node);

    // Setup some stuff that it doesnt want to do on node save for some unknown reason!
    $node->body[LANGUAGE_NONE][0]['safe_value'] = check_markup($node->body[LANGUAGE_NONE][0]['value'], 'filtered_html');

    $node->comment_count = 0;
    $node->name = $user->name;


    return $node;
}

function data_get_statements_for_node($node) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'statement')
        ->propertyCondition('status', 1)
        ->fieldCondition('field_statement_node', 'target_id', $node->nid, '=');
//            ->addMetaData('account', user_load(1)); // Run the query as user 1.
    $results = $query->execute();

    $statements = array();
    $statement_ids = array();
    if(isset($results['node'])) {
        foreach($results['node'] as $statement_id) {
            $statement_ids[] = $statement_id->nid;
        }
        $statements = node_load_multiple($statement_ids);
    }
    $statements = array_merge(array(), $statements);
    $statements = array_reverse($statements);
    return $statements;
}

/**
 * Get all the clinical features of a bone dysplasia
 * @param $bone_dysplasia
 * @return array
 */
function data_get_clinical_features_for_bone_dysplasia($bone_dysplasia) {
    /* Get the Clinical Features */

    $sql = "SELECT t.tid, t.name
            FROM {taxonomy_term_data} t
            RIGHT JOIN {field_data_field_skeletome_tags} f
            ON t.tid = f.field_skeletome_tags_tid
            WHERE f.entity_id = :bid";
    $results = db_query($sql, array(
        'bid'       => $bone_dysplasia->nid
    ));

    $clinical_features = $results->fetchAll();

    return $clinical_features;

//    $clinical_features_array = array();
//    $field_skeletome_tags = field_get_items('node', $bone_dysplasia, 'field_skeletome_tags');
//    $clinical_features_tids = array();
//    if($field_skeletome_tags) {
//        foreach($field_skeletome_tags as $field_skeletome_tag) {
//            $clinical_features_tids[] = $field_skeletome_tag['tid'];
//        }
//        $clinical_features_object = taxonomy_term_load_multiple($clinical_features_tids);
//        // Convert to array
//
//        foreach($clinical_features_object as $clinical_feature) {
//            $clinical_features_array[] = (array)$clinical_feature;
//        }
//    }
//    return $clinical_features_array;
}

/**
 * Get the omim number for a bone dysplasia
 * @param $bone_dysplasia
 * @return mixed
 */
function data_get_omim_for_bone_dysplasia($bone_dysplasia) {
    $field_bd_omim = field_get_items('node', $bone_dysplasia, 'field_bd_omim');
    if($field_bd_omim[0]['value']) {
        return $field_bd_omim[0]['value'];
    } else {
        return "";
    }
}

function data_get_lastest_release_for_all_sources() {
    $sources = data_get_all_sources();

    $releases = array();
    foreach($sources as $source) {
        $source_id = $source->tid;
        $sql = "SELECT t.*, s.sk_gsr_field_timestamp_value
                FROM {taxonomy_term_data} t
                INNER JOIN {field_data_sk_gsr_field_group_source} g
                ON g.entity_id = t.tid
                LEFT JOIN {field_data_sk_gsr_field_timestamp} s
                ON s.entity_id = t.tid
                WHERE g.sk_gsr_field_group_source_tid = $source_id
                ORDER BY s.sk_gsr_field_timestamp_value DESC
                LIMIT 1";
        $results = db_query($sql);
        foreach($results as $result) {
            $releases[] = $result;
        }
    }

    return $releases;
}

function data_get_all_sources() {
    $sk_group_source = taxonomy_vocabulary_machine_name_load('sk_group_source');
    $sk_group_source_vid = $sk_group_source->vid;
    $sql = "SELECT *
                FROM {taxonomy_term_data} t
                WHERE t.vid = $sk_group_source_vid
                ORDER BY t.name ASC";
    $results = db_query($sql);

    $sources = array();
    foreach($results as $result) {
        $sources[] = $result;
    }
    return $sources;
}

function data_get_moi($bone_dysplasia) {
    $field_bd_moi = field_get_items('node', $bone_dysplasia, 'field_bd_moi');

    $moi = null;
    if(isset($field_bd_moi[0])) {
        $moi = taxonomy_term_load($field_bd_moi[0]['tid']);

        // do some slight renaming
        if($moi->name == "Autosomal dominant inheritance") {
            $moi->name = "AD";
        } else if($moi->name == "Autosomal recessive inheritance") {
            $moi->name = "AR";
        } else if($moi->name == "X-linked dominant inheritance") {
            $moi->name = "XL";
        }
    }

    return $moi;
}


/**
 * Get Genes, Gene Mutations and Gene Types for a set of Bone Dysplasias
 * @param $bone_dysplasia_array     Array contain 1 or more Bone Dysplasias
 */
function data_get_genes_for_bone_dysplasias($bone_dysplasia_array) {
    /* Get the Genes */
    // Get Genes Mutations
    $gene_mutation_nids = array();
    foreach($bone_dysplasia_array as $bd) {
        $field_bd_gms = field_get_items('node', $bd, 'field_bd_gm');

        if($field_bd_gms) {
            foreach($field_bd_gms as $field_bd_gm) {
                $gene_mutation_nids[] = $field_bd_gm['target_id'];
            }
        }
    }
    $gene_mutations_full = array_values(node_load_multiple($gene_mutation_nids));

    $genes = array();
    foreach($gene_mutations_full as $gene_mutation_full) {

        // Get the Gene
        $field_gene_mutation_gene = (field_get_items('node', $gene_mutation_full, 'field_gene_mutation_gene'));
        $gene_full = unset_node_trash(node_load($field_gene_mutation_gene[0]['target_id']));

        // Add the Gene to the gene array (if we havent already found it, we want a unique list)
        if(!isset($genes[$gene_full->nid])) {
            $genes[$gene_full->nid] = $gene_full;
            $gene_full->field_gene_gene_mutation = array();
        }

        // Get the Gene Mutation Type
//        $field_gm_mutation_type = (field_get_items('node', $gene_mutation_full, 'field_gm_mutation_type'));
//        $gene_type_full = unset_node_trash(node_load($field_gm_mutation_type[0]['target_id']));

        // Add the Gene Mutation Type To the Gene Mutation
//        $gene_mutation_full->field_gm_mutation_type = $gene_type_full;

        // Add the gene mutation for the gene
//        $gene_full->field_gene_gene_mutation[] = unset_node_trash($gene_mutation_full);

    }


    return array_values($genes);
}


function data_get_groups_and_tags($bone_dysplasia) {
    $field_sk_bd_tags = field_get_items('node', $bone_dysplasia, 'sk_bd_tags');

    $tags = array();

    if($field_sk_bd_tags) {
        foreach($field_sk_bd_tags as &$field_sk_bd_tag) {
            // Save the tag to the list of tags (it has some cruft around it, so we are just geting out the actual tag)

            // Load the tag
            $tag = $field_sk_bd_tag['taxonomy_term'];

            $clean_tag = new stdClass();
            $clean_tag->name = $tag->name;
            $clean_tag->tid = $tag->tid;

            // Load Group name
            $clean_tag->sk_gt_field_group_name = taxonomy_term_load($tag->sk_gt_field_group_name[LANGUAGE_NONE][0]['tid']);

            // Get the source release
            $group_source_release = taxonomy_term_load($tag->sk_gt_field_group_source_release[LANGUAGE_NONE][0]['tid']);

            // We have to manaully make copies, to avoid problems with
            // taxonomy term load caching objects
            // and then it causes problems when i replace reference with fully loaded objects
            $clean_tag->sk_gt_field_group_source_release =  new stdClass();
            $clean_tag->sk_gt_field_group_source_release->tid = $group_source_release->tid;
            $clean_tag->sk_gt_field_group_source_release->name = $group_source_release->name;
            $clean_tag->sk_gt_field_group_source_release->vid = $group_source_release->vid;
            $clean_tag->sk_gt_field_group_source_release->vocabulary_machine_name = $group_source_release->vocabulary_machine_name;
            $clean_tag->sk_gt_field_group_source_release->sk_gsr_field_timestamp = $group_source_release->sk_gsr_field_timestamp;
            $clean_tag->sk_gt_field_group_source_release->sk_gsr_field_group_source = $group_source_release->sk_gsr_field_group_source;


            // Load the source
            $group_source_tid = $group_source_release->sk_gsr_field_group_source[LANGUAGE_NONE][0]['tid'];
            $clean_tag->sk_gt_field_group_source_release->sk_gsr_field_group_source = taxonomy_term_load($group_source_tid);

            $tags[] = $clean_tag;
        }
    }

    return $tags;
}


function data_all_genes($limit = 10) {
    $sql = "SELECT *
                    FROM {node} n
                    WHERE n.type = 'gene'
                    LIMIT $limit";

    $genes = db_query($sql);

    $return_genes = array();
    foreach($genes as $gene) {
        $return_genes[] = $gene;
    }
    return $return_genes;
}
function data_all_gene_mutations($limit = 10) {
    $sql = "SELECT *
                    FROM {node} n
                    WHERE n.type = 'gene_mutation'
                    LIMIT $limit";

    $genes = db_query($sql);

    $return_genes = array();
    foreach($genes as $gene) {
        $return_genes[] = $gene;
    }
    return $return_genes;
}

function data_all_groups($limit = 10) {
    $phenotype_taxonomy = taxonomy_vocabulary_machine_name_load('sk_group_tag');
    $sql = "SELECT *
                    FROM {taxonomy_term_data}
                    WHERE vid = :vid
                    LIMIT $limit";

    $clinical_features = db_query($sql, array(
        'vid'       => $phenotype_taxonomy->vid,
    ));
    $return_clinical_features = array();
    foreach($clinical_features as $clinical_feature) {
        $return_clinical_features[] = $clinical_feature;
    }
    return $return_clinical_features;
}

function data_all_bone_dysplasias($limit = 10, $full = false) {
    $sql = "SELECT *
                    FROM {node} n
                    WHERE n.type = 'bone_dysplasia'
                    LIMIT $limit";

    $bone_dysplasias = db_query($sql);

    $return_bone_dysplasias = array();
    foreach($bone_dysplasias as $bone_dysplasia) {
        $return_bone_dysplasias[] = $bone_dysplasia;
    }

    if(!$full) {
        return $return_bone_dysplasias;
    } else {
        // get the ids
        $ids = array();
        foreach($return_bone_dysplasias as $bone_dysplasia) {
            $ids[] = $bone_dysplasia->nid;
        }

        return array_merge(node_load_multiple($ids));
    }

}

function data_all_clinical_features($limit = 10) {
    $phenotype_taxonomy = taxonomy_vocabulary_machine_name_load('skeletome_vocabulary');
    $sql = "SELECT *
                    FROM {taxonomy_term_data}
                    WHERE vid = :vid
                    LIMIT $limit";

    $clinical_features = db_query($sql, array(
        'vid'       => $phenotype_taxonomy->vid,
    ));
    $return_clinical_features = array();
    foreach($clinical_features as $clinical_feature) {
        $return_clinical_features[] = $clinical_feature;
    }
    return $return_clinical_features;
}


function object_to_array(&$object) {
    $array = array();
    foreach($object as $part) {
        $array[] = $part;
    }
    return $array;
}