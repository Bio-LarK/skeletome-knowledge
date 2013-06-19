<?php
/**
 * Created by JetBrains PhpStorm.
 * User: uqcmcna1
 * Date: 19/03/13
 * Time: 9:47 AM
 * To change this template use File | Settings | File Templates.
 */


function ajax_remove_statement($statement_id) {
    echo "removing $statement_id";
    node_delete($statement_id);
}

function ajax_get_tags_for_release($release_id) {
    $tags = data_get_tags_for_release_id($release_id);
    // Add all the group names for the tags
    foreach($tags as &$tag) {
        // get the group just in case we need it for something
        $tag->sk_gt_field_group_name = taxonomy_term_load($tag->sk_gt_field_group_name[LANGUAGE_NONE][0]['tid']);
    }

    echo drupal_json_encode($tags);
}

function ajax_remove_xray_from_bone_dysplasia($bone_dysplasia_id, $xray_id) {
    $bone_dysplasia = node_load($bone_dysplasia_id);

    // get the xrays

    $newXRays = array();
    if(isset($bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE])) {
        foreach($bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE] as $xray) {
            if($xray['fid'] != $xray_id) {
                $newXRays[] = $xray;
            }
        }
        $bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE] = $newXRays;
    }

    $bone_dysplasia->revision = 1;
    $bone_dysplasia->log = "XRay Removed.";

    node_save($bone_dysplasia);
    echo "xray removed";
}

function ajax_add_existing_xray_to_bone_dysplasia($bone_dysplasia_id, $xray_id) {
    $bone_dysplasia = node_load($bone_dysplasia_id);

    $bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE][] = (array)file_load($xray_id);

    $bone_dysplasia->revision = 1;
    $bone_dysplasia->log = "Added XRay.";
    node_save($bone_dysplasia);


    echo "added exsting xray";
}

/**
 * Updates the Clinical Features on a Bone Dysplasia
 * @param $bone_dysplasia_id    The ID of the bone dysplasia
 */
function ajax_bone_dysplasia_clinical_features($bone_dysplasia_id) {
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $clinical_features = (object) $objData['clinical_features'];

    $bone_dysplasia = node_load($bone_dysplasia_id);
    $bone_dysplasia->field_skeletome_tags[LANGUAGE_NONE] = $clinical_features;
    node_save($bone_dysplasia);

    echo json_encode($bone_dysplasia);
}

/**
 * Updates the XRays attached to a bone dysplasia
 * @param $bone_dysplasia_id    The ID of the bone dysplasia
 */
function ajax_bone_dysplasia_xrays($bone_dysplasia_id) {
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $xrays = (object) $objData['xrays'];

    $bone_dysplasia = node_load($bone_dysplasia_id);
    $bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE] = $xrays;
    node_save($bone_dysplasia);

    echo json_encode($bone_dysplasia);
}


function ajax_add_xray_to_bone_dysplasia($bone_dysplasia_id) {
    // Debugging info

    $_FILES['files'] = array();

    // This is just a random name, could be anything
    $source = "xray_uploader";

    // convert it to format expected by drupal
    foreach($_FILES['file'] as $key => $value) {
        $_FILES['files'][$key][$source] = $value;
    }

    if (array_key_exists('files', $_FILES)) {
        global $base_url, $user;

        //check for folders
        $current_directory = getcwd();
        if (!file_exists($current_directory . '/' . variable_get('file_public_path', conf_path() . '/files') . '/file_uploads')) {
            $result = mkdir($current_directory . '/' . variable_get('file_public_path', conf_path() . '/files') . '/file_uploads');
        }
        if (!file_exists($current_directory . '/' . variable_get('file_public_path', conf_path() . '/files') . '/file_uploads/' . $user->uid)) {
            $result = mkdir($current_directory . '/' . variable_get('file_public_path', conf_path() . '/files') . '/file_uploads/' . $user->uid);
        }

        $secure_file_name = file_munge_filename($_FILES['files']['name'][0], "gif jpeg jpg png tiff");

        $destination_uri = file_build_uri('file_uploads/' . $user->uid);

        $ok = file_save_upload($source, array(), $destination_uri);

        $ok->status = FILE_STATUS_PERMANENT;
        $file = file_save($ok);
        $file->display = 1;

        // Save Bone Dysplasia
        $bone_dysplasia = node_load($bone_dysplasia_id);
        $bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE][] = (array)$file;
        $bone_dysplasia->revision = 1;
        $bone_dysplasia->log = "Added New XRay.";
        node_save($bone_dysplasia);

        watchdog("skeletome builder", "my message");

        $saved_file = $bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE][count($bone_dysplasia->field_bd_xray_images[LANGUAGE_NONE]) - 1];
        $saved_file['full_url'] = file_create_url($saved_file['uri']);
        $saved_file['thumb_url'] = image_style_url('thumbnail', $saved_file['uri']);

        drupal_get_messages();

        echo drupal_json_encode($saved_file);
    }

}

function page_search($type, $query="") {

    echo $query;
    die();
    switch($type) {
        case "all":
            $data = data_search_all($query, 10, 0);
            break;
        case "bone-dysplasias":
            $data = data_search_bone_dysplasias($query, array(), 10, 0);
            break;
        case "genes":
            $data = data_search_genes($query, array(), 10, 0);
            break;
        case "clinical-features":
            $data = data_search_clinical_features($query, array(), 10, 0);
            break;
        case "groups":
            $data = data_search_groups($query, array(), 10, 0);
            break;
        default:
            break;
    }


    // Get the count
    $counts = data_result_counts($query);

    drupal_add_js(array(
        'skeletome_builder' => array(
            "query"             => $query,
            "data"              => array(
                'results'       => $data
            ),
            "counts"            => $counts,
            "type"              => $type
    )), 'setting');

    // Show the search page
    $build['search_title'] = array(
        "#theme"    => "sb_search",
    );
    return $build;
}

function data_result_counts($query) {
    $sql = "SELECT COUNT(node.nid) as 'count'
            FROM node
            LEFT JOIN field_data_body
            ON field_data_body.entity_id = node.nid
            WHERE node.type = 'bone_dysplasia'
            AND (node.title LIKE :term
            OR field_data_body.body_value LIKE :term)";

    $results = db_query($sql, array(
        'term'      => '%' . $query .'%',
    ));
    $bone_dysplasia_count = 0;
    foreach($results as $result) {
        $bone_dysplasia_count = $result->count;
    }

    $sql = "SELECT COUNT(node.nid) as 'count'
            FROM node
            LEFT JOIN field_data_body
            ON field_data_body.entity_id = node.nid
            WHERE node.type = 'gene'
            AND (node.title LIKE :term
            OR field_data_body.body_value LIKE :term)";

    $results = db_query($sql, array(
        'term'      => '%' . $query .'%',
    ));
    $gene_count = 0;
    foreach($results as $result) {
        $gene_count = $result->count;
    }

    $vocab = taxonomy_vocabulary_machine_name_load('skeletome_vocabulary');
    $vocab_id = $vocab->vid;

    $sql = "SELECT COUNT(t.tid) as 'count'
            FROM {taxonomy_term_data} t
            WHERE t.name LIKE :name
            AND t.vid = $vocab_id";

    $results = db_query($sql, array(
        'name'      => '%' . $query .'%',
    ));
    $clinical_feature_count = 0;
    foreach($results as $result) {
        $clinical_feature_count = $result->count;
    }

    $vocab = taxonomy_vocabulary_machine_name_load('sk_group_tag');
    $vocab_id = $vocab->vid;

    $sql = "SELECT COUNT(t.tid) as 'count'
            FROM {taxonomy_term_data} t
            WHERE t.name LIKE :name
            AND t.vid = $vocab_id";

    $results = db_query($sql, array(
        'name'      => '%' . $query .'%',
    ));
    $group_count = 0;
    foreach($results as $result) {
        $group_count = $result->count;
    }

    return array(
        'bone_dysplasia'    => $bone_dysplasia_count,
        'gene'              => $gene_count,
        'clinical_feature'  => $clinical_feature_count,
        'group'             => $group_count
    );
}

function data_search_all($query, $limit, $offset) {
    // Get all nodes
    $sql = "SELECT *
            FROM node
            LEFT JOIN field_data_body
            ON field_data_body.entity_id = node.nid
            WHERE node.title LIKE :term
            OR field_data_body.body_value LIKE :term
            ORDER BY CHAR_LENGTH(node.title) ASC";

    $results = db_query_range($sql, $offset, $limit, array(
        'term'      => '%' . $query .'%'
    ));

    $return_results = array();
    foreach($results as $result) {
        $return_results[] = $result;
    }

    $sql = "SELECT *
            FROM {taxonomy_term_data} t
            WHERE t.name LIKE :name";

    $results = db_query_range($sql, $offset, $limit, array(
        'name'      => '%' . $query .'%',
    ));
    foreach($results as $result) {
        $return_results[] = $result;
    }

    return $return_results;
}

function data_search_bone_dysplasias($query, $conditions_array, $limit, $offset) {


    $joins_sql = "";
    $conditions_sql = "";

    if(count($conditions_array)) {
        $joins_sql = " INNER JOIN field_data_field_skeletome_tags
            ON field_data_field_skeletome_tags.entity_id = node.nid ";
        $conditions_sql = " AND field_data_field_skeletome_tags.field_skeletome_tags_tid IN (9765)";
    }

    $sql = "SELECT *
            FROM node
            LEFT JOIN field_data_body
            ON field_data_body.entity_id = node.nid
             $joins_sql
            WHERE node.type = 'bone_dysplasia'
            AND (node.title LIKE :term
            OR field_data_body.body_value LIKE :term)
             $conditions_sql
            ORDER BY CHAR_LENGTH(node.title) ASC";


    $results = db_query_range($sql, $offset, $limit, array(
        'term'      => '%' . $query .'%',
    ));

    // Get out all the half nodes, and the ids for that nodes
    $bone_dysplasias = array();
    foreach($results as $result) {
        $bone_dysplasias[] = $result;
    }
//    echo "<pre>";
//    print_r($bone_dysplasias);
//    echo "</pre>";
//    die();

    return $bone_dysplasias;
}

function data_clinical_features_for_bone_dysplasia_search() {
//    $sql = "SELECT feature.field_skeletome_tags_tid
//            FROM field_data_field_skeletome_tags as `feature`
//            LEFT JOIN node
//            ON feature.entity_id = node.nid
//            LEFT JOIN field_data_body
//            ON field_data_body.entity_id = node.nid
//             $joins_sql
//            WHERE node.type = 'bone_dysplasia'
//            AND (node.title LIKE :term
//            OR field_data_body.body_value LIKE :term)
//             $conditions_sql
//            ORDER BY CHAR_LENGTH(node.title) ASC";
//    $results = db_query($sql, array(
//        'term'      => '%' . $query .'%',
//    ));
//
//    $blah = array();
//    foreach($results as $result) {
//        $blah[] = $result->field_skeletome_tags_tid;
//    }
//
//    echo "<pre>";
//    print_r($blah);
//    echo "</pre>";
//
//
//    // Need to find all the clinical features these bone dysplasias
//    $full_nodes = array_values(node_load_multiple($bone_dyplasias_ids));
//    $clinical_feature_ids = array();
//    foreach($full_nodes as $full_node) {
//        if(isset($full_node->field_skeletome_tags[LANGUAGE_NONE])) {
//            foreach($full_node->field_skeletome_tags[LANGUAGE_NONE] as $clinical_feature) {
//                $clinical_feature_ids[] = $clinical_feature['tid'];
//            }
//        }
//    }
//    $clinical_feature_ids = array_unique($clinical_feature_ids);
//
//    $clinical_feature_ids_ins = implode(",", $clinical_feature_ids);
//
//    $sql = "SELECT t.tid, t.name
//            FROM {taxonomy_term_data} t
//            WHERE t.tid IN ($clinical_feature_ids_ins)";
//    $results = db_query($sql, array(
//        'term'      => '%' . $query .'%',
//    ));
//
//    $clinical_features = array();
//    foreach($results as $result) {
//        $clinical_features[] = $result;
//    }
}

function data_search_genes($query, $conditions_array, $limit, $offset) {
    $sql = "SELECT *
            FROM {node}
            LEFT JOIN field_data_body
            ON field_data_body.entity_id = node.nid
            WHERE node.type = 'gene'
            AND (node.title LIKE :term
            OR field_data_body.body_value LIKE :term)
            ORDER BY CHAR_LENGTH(node.title) ASC";

    $results = db_query_range($sql, $offset, $limit, array(
        'term'      => '%' . $query .'%',
    ));

    $nodes = array();
    foreach($results as $result) {
        $nodes[] = $result;
    }

    return $nodes;
}

function data_search_groups($query, $conditions_array, $limit, $offset) {
    $vocab = taxonomy_vocabulary_machine_name_load('sk_group_tag');
    $vocab_id = $vocab->vid;

    $sql = "SELECT *
            FROM {taxonomy_term_data} t
            WHERE t.name LIKE :name
            AND t.vid = $vocab_id
            ORDER BY CHAR_LENGTH(t.name) ASC";

    $results = db_query_range($sql, $offset, $limit, array(
        'name'      => '%' . $query .'%',
    ));

    $terms = array();
    foreach($results as $result) {
        $terms[] = $result;
    }
    return $terms;
}

function data_search_clinical_features($query, $conditions_array, $limit, $offset) {
    $vocab = taxonomy_vocabulary_machine_name_load('skeletome_vocabulary');
    $vocab_id = $vocab->vid;

    $sql = "SELECT *
            FROM {taxonomy_term_data} t
            WHERE t.name LIKE :name
            AND t.vid = $vocab_id
            ORDER BY CHAR_LENGTH(t.name) ASC";

    $results = db_query_range($sql, $offset, $limit, array(
        'name'      => '%' . $query .'%',
    ));

    $terms = array();
    foreach($results as $result) {
        $terms[] = $result;
    }
    return $terms;
}



function ajax_search_results() {
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);


    $term = $objData['term'];
    $index = $objData['index'];
    $offset = $objData['offset'];

    if(isset($objData['type'])) {
        // Its a typed search
        $type = $objData['type'];
        $conditions_array = $objData['conditions'];

        // Switch on the content type
        switch($type) {
            case "bone-dysplasia":

                break;
            case "gene":
                break;
            case "clinical-feature":
                break;
            case "group":
                break;
            default:
                break;
        }

    } else {
        // Its an all search
    }

    echo $term;
}

function ajax_save_biblio() {

    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $pubmedId = $objData['pubmedId'];

    // Check that we dont already have this pubmed id stored in our database (it can happen)
    $sql = "SELECT *
            FROM {biblio_pubmed} p
            WHERE biblio_pubmed_id = :pubmedid";
    $results = db_query($sql, array(
        'pubmedid'  => $pubmedId
    ));

    $biblio_ids = array();
    foreach($results as $result) {
        $biblio_ids[] = $result->nid;
    }

    if(count($biblio_ids)) {
        // we found an existing pubmed id
        // lets just return that
        echo drupal_json_encode(array(
            'nid'   => $biblio_ids[0]
        ));

    } else {
        // Doesnt exist in our system yet
        // Off to pubmed
        $Eclient = new BiblioEntrezClient;
        try {
            $result = $Eclient->fetch($pubmedId);
        } catch (Exception $e) {
            form_set_error($e->getMessage());
        }

        global $user;
        $data = NULL;
        if (!isset($result->PubmedArticle)) {
            if (isset($result->PubmedBookArticle)) {
                $data = new BiblioEntrezPubmedBookArticle($result->PubmedBookArticle);
            }
        } else {
            $data = new BiblioEntrezPubmedArticle($result->PubmedArticle);
        }

        if ($data !== NULL) {
            $node_data = $data->getBiblio();
            $node_data['type'] = 'biblio';
            $node_data['uid'] = $user->uid;
            $node = (object) $node_data;
            node_save($node);

            echo drupal_json_encode(array(
                'nid'   => $node->nid
            ));
        } else {
            echo drupal_json_encode(array());
        }
    }



}

function ajax_autocomplete_all($term = "") {
    if($term != "") {
        $results = data_autocomplete_search($term);
        echo drupal_json_encode($results);
    } else {
        echo drupal_json_encode(array());
    }
}

function ajax_autocomplete_bone_dysplasia_groups($term, $offset = 0) {

    $group_name = taxonomy_vocabulary_machine_name_load('sk_group_name');

    $sql = "SELECT *
            FROM {taxonomy_term_data} t
            WHERE t.vid = :vid
            AND t.name LIKE :name
            ORDER BY t.name ASC
            LIMIT 20";

    $results = db_query($sql, array(
        'vid'       => $group_name->vid,
        'name'      => '%' . $term .'%'
    ));

    $group_ids = array();
    foreach($results as $result) {
        $group_ids[] = $result->tid;
    }

    $groups = array_values(taxonomy_term_load_multiple($group_ids));

    echo drupal_json_encode($groups);
}

function ajax_autocomplete_genes($term, $offset = 0) {
    $sql = "SELECT *
            FROM node
            WHERE type = 'gene'
            AND title LIKE :title
            ORDER BY node.title ASC
            LIMIT 20";
    $results = db_query($sql, array(
        'title' => '%' . db_like($term) . '%'
    ));

    $gene_ids = array();
    foreach($results as $result) {
        $gene_ids[] = $result->nid;
    }

    $genes = array_values(node_load_multiple($gene_ids));


    echo drupal_json_encode($genes);
}

function ajax_autocomplete_bone_dysplasias($term, $offset = 0) {

    $sql = "SELECT *
            FROM node
            WHERE type = 'bone_dysplasia'
            AND title LIKE :title
            ORDER BY node.title ASC
            LIMIT 20";
    $results = db_query($sql, array(
        'title' => '%' . db_like($term) . '%'
    ));

    $bone_dysplasia_ids = array();
    foreach($results as $result) {
        $bone_dysplasia_ids[] = $result->nid;
    }

    $bone_dysplasias = array_values(node_load_multiple($bone_dysplasia_ids));

    echo drupal_json_encode($bone_dysplasias);

    /*$data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $entityQueryBD = new EntityFieldQuery();
    $entityNodesBD = $entityQueryBD->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'bone_dysplasia')
        ->propertyCondition('status', 1);


    if(isset($objData['title']) && $objData['title'] != "") {
        $entityNodesBD->fieldCondition('body', 'value', $objData['title'], "CONTAINS");
    }
    if(isset($objData['gene']) && $objData['gene'] != "") {
        // Get the Genes that match
        $sql = "SELECT *
                FROM node
                WHERE type = 'gene'
                AND title LIKE :title
                ORDER BY node.title ASC
                LIMIT 10";
        $results = db_query($sql, array(
            'title' => '%'  . $objData['gene'] . '%'
        ));
        $gene_ids = array();
        foreach($results as $result) {
            $gene_ids[] = $result->nid;
        }

        // Get the Gene Mutations that match
        $entityQueryGM = new EntityFieldQuery();
        $entityNodesGM = $entityQueryGM->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', 'gene_mutation')
            ->propertyCondition('status', 1)
            ->fieldCondition('field_gene_mutation_gene', 'target_id', $gene_ids, "IN");

        $gene_mutations = $entityNodesGM->execute();
        $gene_mutation_ids = get_ids($gene_mutations, 'node');

        // Attach the Gene Mutations to our Bone Dysplasia Query
        $entityNodesBD->fieldCondition('field_bd_gm', 'target_id', $gene_mutation_ids, "IN");
    }
    if(isset($objData['omim']) && $objData['omim'] != "") {
        $entityNodesBD->fieldCondition('field_bd_omim', 'value', $objData['omim'], "CONTAINS");
    }

    // Execute the BD query
    $bds = $entityNodesBD->execute();

    // Load all the Bone Dysplasias
    $bone_dysplasias = node_load_multiple(get_ids($bds, 'node'));

    echo drupal_json_encode($bone_dysplasias);*/
}


function ajax_get_bone_dysplasias_for_tag($tag_id) {

    $entityQuery = new EntityFieldQuery();
    $entityNodes = $entityQuery->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'bone_dysplasia')
        ->propertyCondition('status', 1)
        ->fieldCondition('sk_bd_tags', 'tid', $tag_id)
        ->execute();

    $bone_dysplasias = array_values(unset_node_trash(node_load_multiple(array_keys($entityNodes['node']))));

    echo drupal_json_encode($bone_dysplasias);
}

function ajax_new_bone_dysplasia() {
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $title = $objData['title'];

    $node = data_create_new_bone_dysplasia($title);

    $response = array(
        'nid'    => $node->nid
    );

    echo drupal_json_encode($response);

}

function data_create_new_bone_dysplasia($title, $uri="") {
    global $user;

    $node = new stdClass();
    $node->title = $title;
    $node->type = "bone_dysplasia";
    node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
    $node->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
    $node->uid = $user->uid;
    $node->status = 1; //(1 or 0): published or not
    $node->promote = 0; //(1 or 0): promoted to front page
    $node->comment = 1; //2 = comments on, 1 = comments off
    $node = node_submit($node); // Prepare node for saving
    $node->revision = 1;
    $node->log = "Created new bone dysplasia.";
    if($uri != "") {
        $node->field_bd_uri[LANGUAGE_NONE] = array(
            array(
                'value' => $uri
            )
        );
    }
    node_save($node);

    return $node;
}
/**
 * Get  - Gene mutations for Gene
 */
function ajax_gene_gene_mutations($gene_nid) {
    $gene['nid'] = $gene_nid;
    $gene_mutations = data_get_gene_mutations_for_gene((object)$gene);

    drupal_json_output($gene_mutations);
}


function ajax_new_group_for_bone_dysplasia() {
// Get out Post Request
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $bone_dysplasia_nid = $objData['boneDysplasiaNid'];
    $group_source_tid = $objData['groupSourceTid'];
    $group_source = taxonomy_term_load($group_source_tid);
    $group_source_release_tid = $objData['groupSourceReleaseTid'];
    $group_source_release = taxonomy_term_load($group_source_release_tid);
    $group_name_tid = $objData['groupNameTid'];
    $group_name = taxonomy_term_load($group_name_tid);
    $group_description = $objData['description'];

    $group_tag = taxonomy_vocabulary_machine_name_load('sk_group_tag');

    // Build the name for the tag
    // Source + Month + Year + Group Name
    $name = $group_source->name . ' ' . $group_source_release->name . ' ' . $group_name->name;


    // Create the group
    $term = array(
        'vid' => $group_tag->vid, // Voacabulary ID
        'name' => $name, // Term Name
        'description' => $group_description,
        'sk_gt_field_group_source_release' => array(
            'und' => array(
                array('tid' => $group_source_release_tid)
            )
        ),
        'sk_gt_field_group_name' => array(
            'und' => array(
                array('tid' => $group_name_tid)
            )
        )
    );
    $term = (object)$term;
    taxonomy_term_save($term);

    // Add term to bone dyspalsia
    $bone_dysplasia = node_load($bone_dysplasia_nid);
    $bone_dysplasia->sk_bd_tags[LANGUAGE_NONE][]['tid'] = $term->tid;
    node_save($bone_dysplasia);

    drupal_json_output($term);
}

function ajax_edit_groups_for_bone_dysplasia() {
    // Get out Post REquest
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    // Get BD ID and CF Id
    $bone_dysplasia_nid = $objData['boneDysplasiaNid'];
    $groups =  $objData['groups'];

    $bone_dysplasia = node_load($bone_dysplasia_nid);

    $bone_dysplasia->sk_bd_tags[LANGUAGE_NONE] = array();
    foreach($groups as $group) {
        $bone_dysplasia->sk_bd_tags[LANGUAGE_NONE][]['tid'] = $group['tid'];
    }

    node_save($bone_dysplasia);

    echo "Groups Updated to Bone Dysplasia";
}

function ajax_add_details_to_bone_dysplasia($bone_dysplasia_nid) {
// Get out Post REquest
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    // Get BD ID and CF Id
    $moi_tid = null;
    if(isset($objData['moiTid'])) {
        $moi_tid = $objData['moiTid'];
    };

    // Get BD ID and CF Id
    $omim = null;
    if(isset($objData['omim'])) {
        $omim =  $objData['omim'];
    }

    $bone_dysplasia = node_load($bone_dysplasia_nid);

    if($moi_tid) {
        $bone_dysplasia->field_bd_moi[LANGUAGE_NONE][0]['tid'] = $moi_tid;
    }
    if($omim) {
        $bone_dysplasia->field_bd_omim[LANGUAGE_NONE][0]['value'] = $omim;
    }

    $bone_dysplasia->revision = 1;
    $bone_dysplasia->log = "Edited Details.";

    node_save($bone_dysplasia);

    echo "Details updated";

}

//function ajax_edit_moi_to_bone_dysplasia($bone_dysplasia_nid) {
//    // Get out Post REquest
//    $data = file_get_contents("php://input");
//    $objData = json_decode($data, true);
//
//    // Get BD ID and CF Id
//    $moi_tid =  $objData['moiTid'];
//
//    print $moi_tid;
//
//
//}
//
//function ajax_add_omim_to_bone_dysplasia($bone_dysplasia_nid) {
//    // Get out Post REquest
//    $data = file_get_contents("php://input");
//    $objData = json_decode($data, true);
//
//    // Get BD ID and CF Id
//    $omim =  $objData['omim'];
//
//    $bone_dysplasia = node_load($bone_dysplasia_nid);
//
//
//
//    $bone_dysplasia->revision = 1;
//    $bone_dysplasia->log = "Edited OMIM.";
//    node_save($bone_dysplasia);
//
//    echo "OMIM Updated to Bone Dysplasia";
//}





function ajax_add_gene_to_bone_dysplasia($bone_dysplasia_id) {
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    if(isset($objData['geneName'])) {
        global $user;
        $node = new stdClass();
        $node->title = $objData['geneName'];
        $node->type = "gene";
        node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
        $node->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
        $node->uid = $user->uid;
        $node->status = 1; //(1 or 0): published or not
        $node->promote = 0; //(1 or 0): promoted to front page
        $node->comment = 1; //2 = comments on, 1 = comments off
        $node = node_submit($node); // Prepare node for saving
        $node->revision = 1;
        $node->log = "Created new Gene.";
        node_save($node);
        $gene_nid = $node->nid;
    } else {
        $gene_nid = $objData['geneNid'];
    }

    // Find a gene mutation that is an 'unspecified gene mutation'
    // Maybe it should just make it?
    $gene_mutation = data_get_unspecified_mutation_for_gene($gene_nid);
    $bone_dysplasia = data_add_gene_mutation_to_bone_dysplasia($bone_dysplasia_id, $gene_mutation->nid);

    echo drupal_json_encode(unset_node_trash(node_load($gene_nid)));
}

function ajax_remove_gene_to_bone_dysplasia($bone_dysplasia_nid, $gene_nid) {
    // Find all the gene mutations, that are linking to the bone dysplasia
    $gene_mutations = data_get_gene_mutations_for_gene(node_load($gene_nid));

    $gene_mutations_ids = array();
    foreach($gene_mutations as $gene_mutation) {
        $gene_mutations_ids[] = $gene_mutation->nid;
    }


    $bone_dysplasia = node_load($bone_dysplasia_nid);

    if(isset($bone_dysplasia->field_bd_gm[LANGUAGE_NONE])) {
        foreach($bone_dysplasia->field_bd_gm[LANGUAGE_NONE] as $gene_mutation_target) {
            $gene_mutation_id = $gene_mutation_target['target_id'];
            if(in_array($gene_mutation_id, $gene_mutations_ids)) {
                // this is a gene mutation to remove
                data_remove_gene_mutation_from_bone_dysplasia($bone_dysplasia_nid, $gene_mutation_id);
            }
        }
    }
}

function data_remove_gene_mutation_from_bone_dysplasia($bone_dysplasia_nid, $gene_mutation_nid) {
    $bone_dysplasia = node_load($bone_dysplasia_nid);

    if(isset($bone_dysplasia->field_bd_gm[LANGUAGE_NONE])) {

        $gene_mutations = $bone_dysplasia->field_bd_gm[LANGUAGE_NONE];
        for($i = 0; $i < count($gene_mutations); $i++) {
            $gene_mutation = $gene_mutations[$i];

            if($gene_mutation['target_id'] == $gene_mutation_nid) {
                unset($bone_dysplasia->field_bd_gm[LANGUAGE_NONE][$i]);
            }
        }
        node_save($bone_dysplasia);
    }

    // Now remove the bone dysplasia from the gene mutation
    $gene_mutation = node_load($gene_mutation_nid);
    $bone_dysplasias = $gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE];
    helper_pprint($bone_dysplasias);
    for($i = 0; $i < count($bone_dysplasias); $i++) {
        $bone_dysplasiaLoop = $bone_dysplasias[$i];

        if($bone_dysplasiaLoop['target_id'] == $bone_dysplasia->nid) {
            unset($gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE][$i]);
        }
    }
    node_save($gene_mutation);
}

function data_get_unspecified_mutation_for_gene($gene_nid) {

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'gene_mutation')
        ->propertyCondition('status', 1)
        ->fieldCondition('field_gene_mutation_gene', 'target_id', $gene_nid, '=')
        ->propertyCondition('title', 'unspecified', 'CONTAINS');
    $results = $query->execute();

    $gene_mutation_ids = array();
    if(isset($results['node'])) {
        $gene_mutation_ids = array_keys($results['node']);
        return node_load($gene_mutation_ids[0]);
    } else {
        return data_create_gene_mutation_for_gene($gene_nid, + ' (unspecified mutation)');
    }
}




function data_add_gene_mutation_to_bone_dysplasia($bone_dysplasia_nid, $gene_mutation_nid) {
    $bone_dysplasia = node_load($bone_dysplasia_nid);
    // Add gene mutation to bone dysplasia
    $bone_dysplasia->field_bd_gm[LANGUAGE_NONE][] = array(
        'target_id' => $gene_mutation_nid
    );
    node_save($bone_dysplasia);

    // Add bone dysplasia to gene mutation
    $gene_mutation = node_load($gene_mutation_nid);
    $gene_mutation->field_gene_mutation_bd[LANGUAGE_NONE][] = array(
        'target_id' => $bone_dysplasia->nid
    );
    node_save($gene_mutation);

    return $bone_dysplasia;
}

function ajax_add_gene_mutation_to_bone_dysplasia($bone_dysplasia) {
    // Get out Post Request
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    // Check if its an existing or new gene mutation
    if(isset($objData['geneMutationNid'])) {
        $gene_mutation_nid =  $objData['geneMutationNid'];
    } else {
        $gene_mutation_title = $objData['geneMutationTitle'];
        $gene_nid = $objData['geneNid'];
        $gene_mutation = data_create_gene_mutation_for_gene($gene_nid, $gene_mutation_title);
        $gene_mutation_nid = $gene_mutation->nid;
    }

    data_add_gene_mutation_to_bone_dysplasia($bone_dysplasia->nid, $gene_mutation_nid);

    echo drupal_json_encode(unset_node_trash($gene_mutation));
}



function ajax_remove_gene_mutation_from_bone_dysplasia($bone_dysplasia, $gene_mutation_nid) {

    // Remopve the Gene mutation from the bone dysplasia
    data_remove_gene_mutation_from_bone_dysplasia($bone_dysplasia->nid, $gene_mutation_nid);

    echo "Gene Mutation Removed from Bone Dysplasia";
}

function ajax_add_clinical_feature_to_bone_dysplasia() {
    // Get out Post REquest
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    // Get BD ID and CF Id
    $bone_dysplasia_nid = $objData['boneDysplasiaNid'];
    $clinical_feature_tid =  $objData['clinicalFeatureTid'];

    $bone_dysplasia = node_load($bone_dysplasia_nid);

    $bone_dysplasia->field_skeletome_tags[LANGUAGE_NONE][] = array(
        'tid' => $clinical_feature_tid
    );
    $bone_dysplasia->revision = 1;
    $bone_dysplasia->log = "Added clinical feature.";
    node_save($bone_dysplasia);

    // Calculate the information content for the clinical feature
    $clinical_feature = taxonomy_term_load($clinical_feature_tid);
    $clinical_feature->information_content = data_get_information_content_for_feature(
        count(data_get_bone_dysplasias_for_clinical_feature($clinical_feature)),
        data_get_bone_dysplasia_count(),
        data_get_max_clinical_feature_count()
    );

//    echo $clinical_feature->information_content;
    echo drupal_json_encode($clinical_feature);
}
function ajax_remove_clinical_feature_from_bone_dysplasia() {
    // Get out Post Request
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    // Get BD ID and CF Id
    $bone_dysplasia_nid = $objData['boneDysplasiaNid'];
    $clinical_feature_tid =  $objData['clinicalFeatureTid'];

    $bone_dysplasia = node_load($bone_dysplasia_nid);
    $skeletome_tags = $bone_dysplasia->field_skeletome_tags[LANGUAGE_NONE];
    for($i = 0; i < count($skeletome_tags); $i++) {
        $tag = $skeletome_tags[$i];
        if($tag['tid'] == $clinical_feature_tid) {
            unset($bone_dysplasia->field_skeletome_tags[LANGUAGE_NONE][$i]);
            break;
        }
    }

    $bone_dysplasia->revision = 1;
    $bone_dysplasia->log = "Removed clinical feature.";
    node_save($bone_dysplasia);
    echo "Clinical Feature Removed from Bone Dysplasia";
}

function ajax_gene_details($gene_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Updating a Gene
        $data = file_get_contents("php://input");
        $objData = json_decode($data, true);

        $locus = $objData['locus'];
        $mesh = $objData['mesh'];
        $omim = $objData['omim'];
        $umls = $objData['umls'];
        $uniprot = $objData['uniprot'];
        $accession = $objData['accession'];
        $entrez = $objData['entrez'];
        $refseq = $objData['refseq'];

        $gene = node_load($gene_id);
        $gene->field_gene_locus[LANGUAGE_NONE][0]['value'] = $locus;
        $gene->field_gene_mesh[LANGUAGE_NONE][0]['value'] = $mesh;
        $gene->field_gene_omim[LANGUAGE_NONE][0]['value'] = $omim;
        $gene->field_gene_umlscui[LANGUAGE_NONE][0]['value'] = $umls;
        $gene->field_gene_uniprot[LANGUAGE_NONE][0]['value'] = $uniprot;
        $gene->field_gene_accession[LANGUAGE_NONE][0]['value'] = $accession;
        $gene->field_gene_entrezgene[LANGUAGE_NONE][0]['value'] = $entrez;
        $gene->field_gene_refseq[LANGUAGE_NONE][0]['value'] = $refseq;

        $gene->revision = 1;
        $gene->log = "Edited Gene Details.";
        node_save($gene);

        print_r($locus);
    }
}
function ajax_gene_description($gene_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = file_get_contents("php://input");
        $objData = json_decode($data, true);

        $description = $objData['description'];

        $gene = node_load($gene_id);
        $gene->body[LANGUAGE_NONE][0]['value'] = $description;
        $gene->body[LANGUAGE_NONE][0]['format'] = 'filtered_html';
        $gene->revision = 1;
        $gene->log = "Edited Gene Body text.";
        node_save($gene);




        $gene = node_load($gene_id);

        // Get ouf the safe and non safe versions of the description
        echo drupal_json_encode(array(
            'value'         => $gene->body[LANGUAGE_NONE][0]['value'],
            'safe_value'    => check_markup($gene->body[LANGUAGE_NONE][0]['value'], 'filtered_html')
        ));

    } else {
        echo "not post";
    }
}
function ajax_gene_statement($gene_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = file_get_contents("php://input");
        $objData = json_decode($data, true);

        $statement = data_create_statement_for_node($objData['statement'], $gene_id);
        echo drupal_json_encode($statement);
    } else {
        echo "ERROR";
    }
}

function ajax_create_gene_mutation_for_gene($gene_id) {
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $title = $objData['title'];

    $gene_mutation = data_create_gene_mutation_for_gene($gene_id, $title);

    echo drupal_json_encode($gene_mutation);
}



function ajax_gene_mutation_description($gene_mutation_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = file_get_contents("php://input");
        $objData = json_decode($data, true);

        $description = $objData['description'];

        echo "description is " . $description . "hello";
        $gene_mutation = node_load($gene_mutation_id);
        $gene_mutation->body[LANGUAGE_NONE][0]['value'] = $description;
        node_save($gene_mutation);

        print_r($gene_mutation);
    } else {
        echo "not post";
    }
}

function ajax_bone_dysplasia_description() {

    // The request is a JSON request.
    // We must read the input.
    // $_POST or $_GET will not work!
    $data = file_get_contents("php://input");
    $objData = json_decode($data, true);

    $bone_dysplasia_id = $objData['id'];
    $description = $objData['description'];

    $bone_dysplasia = node_load($bone_dysplasia_id);
    $bone_dysplasia->body[LANGUAGE_NONE][0]['value'] = $description;
    $bone_dysplasia->body[LANGUAGE_NONE][0]['format'] = 'filtered_html';
    $bone_dysplasia->revision = 1;
    $bone_dysplasia->log = "Edited body text.";

    node_save($bone_dysplasia);

    echo drupal_json_encode(array(
        'value'         => $bone_dysplasia->body[LANGUAGE_NONE][0]['value'],
        'safe_value'    => check_markup($bone_dysplasia->body[LANGUAGE_NONE][0]['value'], 'filtered_html')
    ));

}

function ajax_search_genes($search) {
    $return_genes = array();
    if ($search) {
        // Clean it up a little
        $search = str_replace(array(",","-"), "", $search);

        // search for a gene name
        $sql = "SELECT *
                FROM {{node}} n
                WHERE type = 'gene'
                AND n.title LIKE :name";

        $genes = db_query($sql, array(
            'name'      => '%' . db_like($search) . '%'
        ));

        $gene_ids = array();
        foreach($genes as $gene) {
            $gene_ids[] = $gene->nid;
        }

        $genes_full = unset_node_trash(node_load_multiple($gene_ids), false);

        foreach($genes_full as &$gene) {
            $gene->field_gene_gene_mutation = unset_node_trash(data_get_gene_mutations_for_gene($gene), false);

        }

        $return_genes = array_values($genes_full);

//        $sql = "SELECT gmg.revision_id, g.title as 'gene_title', g.vid as 'gene_vid', gm.title as 'gene_mutation_title', gm.vid as 'gm_vid'
//                FROM {field_data_field_gene_mutation_gene} gmg, {node} g, {node} gm
//                WHERE gm.vid = gmg.entity_id
//                AND g.vid = gmg.field_gene_mutation_gene_target_id
//                AND (
//                  CONCAT_WS(' ', g.title, REPLACE(gm.title, ',', '')) LIKE :name
//                  OR
//                  CONCAT_WS(' ', REPLACE(gm.title, ',', ''), g.title) LIKE :name
//                )
//                LIMIT 20";



//        $return_genes = array();
//        foreach($genes as $gene) {
//            $return_genes[] = array(
//                'gene'  => array('nid' => $gene->gene_vid, 'title' => $gene->gene_title),
//                'gene_mutation' => array('nid' => $gene->gm_vid, 'title' => $gene->gene_mutation_title)
//            );
//        }
    }
    drupal_json_output($return_genes);
}
/**
 * Find the first 10 groups thats match the searhc term
 * @param $search
 */
function ajax_autocomplete_groups($search) {
    $groups = array();
    if ($search) {
        $group_taxonomy = taxonomy_vocabulary_machine_name_load('sk_group_tag');

        $sql = "SELECT *
                FROM {taxonomy_term_data}
                WHERE vid = :vid
                AND name LIKE :name
                LIMIT 20";

        $groups = db_query($sql, array(
            'vid'       => $group_taxonomy->vid,
            'name'      => '%' . db_like($search) . '%'
        ));
    }
    drupal_json_output($groups);
}


function ajax_bone_dysplasia_genes($node) {

    // Get out all clinical features

    // Get all clincal features IDs
}