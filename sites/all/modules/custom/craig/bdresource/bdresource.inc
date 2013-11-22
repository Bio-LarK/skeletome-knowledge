<?php
// bdresource.inc
/**
 * Callback for creating bd resources.
 *
 * @param object $data
 * @return object
 */
function _bdresource_create($data) {
    global $user;
    if (isset($data) &&  is_array($data)) $data=(object) $data;
    unset($data->id);
    $data = (object)$data;
    $data->uid = $user->uid;
    $data->created = time();
    $data->modified = time();

    bd_save($data);

    return $data;
}

// bdresource.inc
/**
 * Callback for updating bd resources.
 *
 * @param int $id
 * @param object $data
 * @return object
 */
function _bdresource_update($id, $data) {

    global $user;

    if (isset($data) &&  is_array($data)) $data=(object) $data;
    unset($data->created);
    $data->id = $id;
    $data->uid = $user->uid;
    $data->modified = time();

    bd_save($data);
    return $data;
}
/**
 * Callback for retrieving bd resources.
 *
 * @param int $id
 * @return object
 */
function _bdresource_retrieve($uri) {

    print_r($uri);

    die();



    die();
}

/**
 * Callback for deleting bd resources.
 *
 * @param int $id
 * @return object
 */
function _bdresource_delete($id) {
    bd_delete($id);

    return (object)array(
        'id' => $id,
    );
}



function _bdresource_index($uri) {

    $sql = "SELECT node.nid, node.title, field_data_body.body_value
            FROM node, field_data_field_bd_uri, field_data_body
            WHERE field_data_field_bd_uri.field_bd_uri_value = :uri
            AND node.nid = field_data_field_bd_uri.entity_id
            AND field_data_body.entity_id = field_data_field_bd_uri.entity_id";
    $results = db_query($sql, array(
        'uri'   => $uri
    ))->fetch();

    $bone_dysplasia = $results;

    $sql = "SELECT taxonomy_term_data.*, field_data_field_term_uri.field_term_uri_value as 'uri'
            FROM taxonomy_term_data, field_data_field_skeletome_tags, field_data_field_term_uri
            WHERE field_data_field_skeletome_tags.entity_id = :bd_id
            AND taxonomy_term_data.tid = field_data_field_skeletome_tags.field_skeletome_tags_tid
            AND field_data_field_term_uri.entity_id = field_data_field_skeletome_tags.field_skeletome_tags_tid";
    $results = db_query($sql , array(
        'bd_id' => $bone_dysplasia->nid
    ));
    $bone_dysplasia->clinical_features = $results->fetchAll();


    return $bone_dysplasia;
}


/**
 * Access callback for the bd resource.
 *
 * @param string $op
 *  The operation that's going to be performed.
 * @param array $args
 *  The arguments that will be passed to the callback.
 * @return bool
 *  Whether access is given or not.
 */

function _bdresource_access($op, $args) {
    return true;
}
//
//function _bdresource_access($op, $args) {
//    global $user;
//    $access = FALSE;
//
//    switch ($op) {
//        case 'view':
////            $bd = bd_load($args[0]);
//            $access = user_access('bd resource view any bd');
////            $access = $access || $bd->uid == $user->uid && user_access('bd resource view own bds');
//            break;
//        case 'update':
//            $bd = bd_load($args[0]->id);
//            $access = user_access('bd resource edit any bd');
//            $access = $access || $bd->uid == $user->uid && user_access('bd resource edit own bds');
//            break;
//        case 'delete':
//            $bd = bd_load($args[0]);
//            $access = user_access('bd resource delete any bd');
//            $access = $access || $bd->uid == $user->uid && user_access('bd resource delete own bds');
//            break;
//    }
//    $access = TRUE;
//
//    return $access;
//}