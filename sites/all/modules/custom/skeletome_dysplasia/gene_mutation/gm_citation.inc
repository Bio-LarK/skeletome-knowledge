<?php

function add_gm_citation_callback() {
  $gmNid = $_POST['gmNid'];
  $bdNid = $_POST['bdNid'];
  $pubNid = $_POST['pubNid'];

  $bd_node = node_load($bdNid);
  if (array_key_exists('und', $bd_node->field_bd_citationcontext)) {
    $scc_nids = get_scc_for_gmnid($gmNid);
    $existing_scc = NULL;

    foreach ($bd_node->field_bd_citationcontext['und'] as $scc) {
      if (in_array($scc['nid'], $scc_nids)) {
	$existing_scc = $scc['nid'];
	break;
      }
    }
    if ($existing_scc != NULL) {
      $scc_node = node_load($existing_scc);
      if (!in_array($pubNid, $scc_node->field_ssc_biblio['und'])) {
	$count = count($scc_node->field_ssc_biblio['und']);
	$scc_node->field_ssc_biblio['und'][$count]['nid'] = $pubNid;
	node_save($scc_node);
	add_scc_to_biblio($pubNid, $scc_node->nid);
      }
    } else {
	debug('NOT EXISTING');
      $scc_nid = create_gm_scc($bd_node->nid, $gmNid, $pubNid);

      $count = count($bd_node->field_bd_citationcontext['und']);
      $bd_node->field_bd_citationcontext['und'][$count]['nid'] = $scc_nid;
      node_save($bd_node);

      $gm_node = node_load($gmNid);
      $gm_node->field_gene_mutation_ssc['und'][0]['nid'] = $scc_nid;
      node_save($gm_node);
      add_scc_to_biblio($pubNid, $scc_nid);
    }
  } else {
    $scc_nid = create_gm_scc($bd_node->nid, $gmNid, $pubNid);
    $bd_node->field_bd_citationcontext['und'][0]['nid'] = $scc_nid;
    node_save($bd_node);

    $gm_node = node_load($gmNid);
    $gm_node->field_gene_mutation_ssc['und'][0]['nid'] = $scc_nid;
    node_save($gm_node);

    add_scc_to_biblio($pubNid, $scc_nid);
  }

  drupal_goto('node/' . $gmNid);
}

function delete_gm_citation_callback($scc_nid, $biblio_nid) {
  $scc_node = node_load($scc_nid);
  $gm_nid = $scc_node->field_ssc_genemutation['und'][0]['nid'];

  $to_keep = array();
  foreach ($scc_node->field_ssc_biblio['und'] as $pub) {
    if ($pub['nid'] != $biblio_nid) {
      $to_keep[] = $pub;
    }
  }

  if (!empty($to_keep)) {
    $scc_node->field_ssc_biblio['und'] = $to_keep;
    node_save($scc_node);
    
    $biblio_node = node_load($biblio_nid);
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);
  } else {
    $biblio_node = node_load($biblio_nid);
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);

    $gm_node = node_load($gm_nid);
    remove_from_array($gm_node, 'field_gene_mutation_ssc', 'nid', $scc_node->nid, TRUE);

    $bd_node = node_load($scc_node->field_ssc_bonedysplasia['und'][0]['nid']);
    remove_from_array($bd_node, 'field_bd_citationcontext', 'nid', $scc_node->nid, TRUE);
    
    node_delete($scc_node->nid);
  } 

  drupal_goto('node/' . $gm_nid);
}

function get_scc_for_gmnid($gm_nid) {
  $scc_nids = array();
  $sql = 'SELECT entity_id, field_ssc_genemutation_nid FROM {field_data_field_ssc_genemutation} WHERE field_ssc_genemutation_nid = :gm_nid';
  $result = db_query($sql, array(':gm_nid' => $gm_nid));
  foreach ($result as $row) {
    $scc_nids[] = $row->entity_id;
  }
  return $scc_nids;
}

function create_gm_scc($bdNid, $gm_nid, $pubNid) {
  global $user;

  $scc_node = new stdClass();
  $scc_node->type = 'skeletome_citation_context';
  $scc_node->language = 'und';
  $scc_node->title = 'SCC';
  $scc_node->uid = $user->uid;
  $scc_node->field_ssc_bonedysplasia['und'][0]['nid'] = $bdNid;
  $scc_node->field_ssc_biblio['und'][0]['nid'] = $pubNid;
  $scc_node->field_ssc_genemutation['und'][0]['nid'] = $gm_nid;
  node_save($scc_node);

  return $scc_node->nid;
}

function create_html_for_scc($gmNid, $biblio_node, $scc_nid) {
  global $base_url;

  $title = $biblio_node->title;
  if (strlen($title) > 60) {
    $title = substr($title, 0, 60) . ' ...';
  }

  $biblio_html = '<h3><a href="#">' . $title . '</a></h3>';
  $delete_link = '<a id="delete_button_' . $biblio_node->nid . '" href="' . $base_url . '/node/' . $gmNid . '/citation/delete/' . $scc_nid . '/' . $biblio_node->nid . '">Delete</a>';

  $title_html = '<strong>Title: </strong>' . $biblio_node->title . '<br />';
  $content = '<p>' . $title_html;

  $authors_html = NULL;
  $authors = get_authors_for_biblio($biblio_node->nid);
  if ($authors != NULL) {
    $authors_html = '<strong>Authors: </strong>' . $authors . '<br />';
    $content = $content . $authors_html;
  }

  $abstract = $biblio_node->biblio_abst_e;
  if ($abstract !== '') {
    $abstrac_html = '<strong>Abstract: </strong>' . $abstract . '<br />';
    $content = $content . $abstrac_html;
  }

  $pub_link = '[<a href="' . $biblio_node->nid . '">Go to publication</a>]';
  $content = $content . '<br />' . $pub_link;

  $pubmed_link = $biblio_node->biblio_custom1;
  if ($pubmed_link !== '') {
    $pubmed_html = '[<a href="' . $pubmed_link . '">PubMed</a>]';
    $content = $content . ' ' . $pubmed_html;
  }

  $content = $content . '</p>';

  if (user_is_logged_in()) {
    $biblio_html = $biblio_html . '<div>' . $content . '<div style="text-align:right;">' . $delete_link . '</div></div>';
  } else {
    $biblio_html = $biblio_html . '<div>' . $content . '</div>';
  }

  return $biblio_html;
}
