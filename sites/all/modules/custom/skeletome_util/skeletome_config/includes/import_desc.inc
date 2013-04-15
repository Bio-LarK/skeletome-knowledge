<?php

function import_descriptions($desc_folder) {
  if (is_dir($desc_folder)) {
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
  }
}

function get_bd_for_omim($omim) {
  $sql = 'SELECT entity_id, field_bd_omim_value FROM {field_data_field_bd_omim} fo WHERE fo.field_bd_omim_value = :field_bd_omim_value';
  $result = db_query($sql, array(':field_bd_omim_value' => $omim));
  foreach ($result as $row) {
    return $row->entity_id;
  }

  return NULL;
}

function add_content_to_bd($bd_nid, $content) {
  $bd_node = node_load($bd_nid);
  $bd_node->body['und'][0]['value'] = $content;
  $bd_node->body['und'][0]['safe_value'] = $content;
  $bd_node->body['und'][0]['format'] = 'filtered_html';
  node_save($bd_node);
}