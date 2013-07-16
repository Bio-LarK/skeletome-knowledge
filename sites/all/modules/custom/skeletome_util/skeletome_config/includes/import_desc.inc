<?php

function import_descriptions($desc_folder)
{
    // get all the bone dysplasia
    $sql = "SELECT n.nid
            FROM {node} n
            WHERE type = 'bone_dysplasia'";
    $result = db_query($sql);
    $nids = array_keys($result->fetchAllAssoc('nid'));

    $bone_dysplasias = node_load_multiple($nids);

    echo "looking at " . count($bone_dysplasias) . " bone dysplasias<br/>";

    foreach($bone_dysplasias as $bone_dysplasia) {

        // Get the OMIM number
        if(isset($bone_dysplasia->field_bd_omim[LANGUAGE_NONE])) {
            $omim = $bone_dysplasia->field_bd_omim[LANGUAGE_NONE][0]['value'];
        }

        if($omim && strlen($omim)) {
            $found = false;

            if ($dh = opendir(dirname(__FILE__) . '/texts/genereviews')) {

                // look at all the files
                while (($file = readdir($dh)) !== false) {
                    // Ignore these folders
                    if(strpos($file, $omim) !== FALSE) {
                        echo "adding content genereviews<br/>";
                        $contents = file_get_contents(dirname(__FILE__) . '/texts/genereviews/' . $file);
                        $contents = '<p>'. str_replace('\n\n', '</p><p>', $contents) .'</p>';

                        $bone_dysplasia->body['und'][0]['value'] = $contents;
                        $bone_dysplasia->body['und'][0]['safe_value'] = $contents;
                        $bone_dysplasia->body['und'][0]['format'] = 'filtered_html';
                        node_save($bone_dysplasia);

                        $found = true;
                        break;
                    }
                }
            }
            closedir($dh);

            if(!$found) {
                if ($dh = opendir(dirname(__FILE__) . '/texts/omim_clean')) {
                    // look at all the files
                    while (($file = readdir($dh)) !== false) {
                        // Ignore these folders
                        if(strpos($file, $omim) !== FALSE ) {
                            $contents = file_get_contents(dirname(__FILE__) . '/texts/omim_clean/' . $file);
                            $contents = '<p>'. str_replace('\n\n', '</p><p>', $contents) .'</p>';

                            $bone_dysplasia->body['und'][0]['value'] = $contents;
                            $bone_dysplasia->body['und'][0]['safe_value'] = $contents;
                            $bone_dysplasia->body['und'][0]['format'] = 'filtered_html';
                            node_save($bone_dysplasia);

                            $found = true;
                            break;
                        }
                    }
                }
                closedir($dh);
            }
        }

    }

    /*if (is_dir($desc_folder)) {
        if ($dh = opendir($desc_folder)) {
            while (($file = readdir($dh)) !== false) {
                if (($file === '..') || ($file === '.')) {
                    continue;
                }
                $idx = strpos($file, '-');
                $content = file_get_contents($desc_folder . '/' . $file);
                if ($idx === false) {
                    $bd_nid = get_bd_for_omim($file);
                    add_content_to_bd($bd_nid, $content);
                } else {
                    $omims = explode("-", $file);
                    foreach ($omims as $omim) {
                        $bd_nid = get_bd_for_omim($omim);
                        add_content_to_bd($bd_nid, $content);
                    }
                }
            }
            closedir($dh);
        }
    }*/
}

function get_bd_for_omim($omim)
{
    $sql = 'SELECT entity_id, field_bd_omim_value
            FROM {field_data_field_bd_omim} fo
            WHERE fo.field_bd_omim_value = :field_bd_omim_value';
    $result = db_query($sql, array(
        ':field_bd_omim_value' => $omim
    ));
    foreach ($result as $row) {
        return $row->entity_id;
    }

    return NULL;
}

function add_content_to_bd($bd_nid, $content)
{
    $bd_node = node_load($bd_nid);
    $bd_node->body['und'][0]['value'] = $content;
    $bd_node->body['und'][0]['safe_value'] = $content;
    $bd_node->body['und'][0]['format'] = 'filtered_html';
    node_save($bd_node);
}