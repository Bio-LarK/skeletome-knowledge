<?php

function bd_phenotype_callback($node) {
  $form_state['build_info']['args'] = array($node);
  return drupal_build_form('bd_phenotype_form', $form_state);
}

function bd_phenotype_form($node, &$form_state) {
  $form = NULL;
  $node = $form_state['build_info']['args'][0];
  drupal_add_library('system', 'ui.core');
  drupal_add_library('system', 'ui.accordion');
  drupal_add_library('system', 'ui.button');
  drupal_add_library('system', 'ui.dialog');
  drupal_add_library('system', 'ui.widget');
  drupal_add_library('system', 'ui.position');
  drupal_add_library('system', 'ui.size');
  drupal_add_library('system', 'ui.autocomplete');
  drupal_add_css(drupal_get_path('module', 'bone_dysplasia') . '/bd_pheno.css', array('group' => CSS_DEFAULT));
  drupal_add_js(drupal_get_path('module', 'bone_dysplasia') .'/bd_pheno.js', array('group' => JS_DEFAULT));

  $weight = 1;

  if (is_current_user_editor()) {
    $form['add_pheno_button'] = array(
      '#markup' => '<div style="text-align:right;"><a id="add_pheno_button" href="#">Add Phenotype</a></div><hr style="border:dashed #CCCCCC; border-width:1px 0 0; height:0;" />',
      '#weight' => $weight
    );
    $weight++;
  }

  if (array_key_exists('und', $node->field_skeletome_tags)) {
    $bd_nodes = get_nodes_of_type('bone_dysplasia');
    $html_content = '<div class="accordion">';
    $tags = array();

    foreach($node->field_skeletome_tags['und'] as $pheno_tag) {
      $term = taxonomy_term_load($pheno_tag['tid']);
      $tags[$pheno_tag['tid']] = $term->name;
    }

    asort($tags);
    $count = 0;
    $tids = array();
    foreach ($tags as $tid => $term) {
      $nids = get_nodes_with_tag($tid);
      $intersect = array_intersect_key($nids, $bd_nodes);

      $term_html = create_html_for_pheno($count, $tid, $node->nid, $term, count($intersect));
      $html_content = $html_content . $term_html;

      $tids[$count] = $tid;
      $count++;
    }

    drupal_add_js(array('bdNodeId' => $node->nid), 'setting');
    drupal_add_js(array('noPhenos' => count($tids)), 'setting');
    drupal_add_js(array('tids' => $tids), 'setting');

    $html_content = $html_content . '</div>';
    $form['pheno_title'] = array(
      '#markup' => '<div class="block-title"><h2 class="title">Phenotypic characteristics</h2></div>',
      '#weight' => ($weight + 1)
    );
    $form['pheno'] = array(
      '#markup' => $html_content,
      '#weight' => ($weight + 2)
    );
  }

  $form['add_pheno_form'] = array(
    '#markup' => '<div id="add_pheno_dialog" title="Add phenotype"><form>
      <div class="ui-widget"><div style="float:left;"><label for="phenotype">Phenotype: </label></div><div style="float:right;"><input id="phenotype" /></div></div>
      </form></div>',
    '#weight' => 100
  );

  $form['add_scc_form'] = array(
    '#markup' => '<div id="add_scc_dialog" title="Add citation"><form>
      <div class="ui-widget"><div style="float:left;"><label for="publication">Publication: </label></div><div style="float:right;"><input id="publication" /></div></div>
      </form></div>',
    '#weight' => 100
  );

  if ($form != NULL) {
    return $form;
  }

}

function create_html_for_pheno($idx, $tid, $bd_nid, $term, $count) {
  global $base_url;
  $base_path = url(NULL, array('absolute' => TRUE));

  $term_html = '<h3><a href="#">' . $term . '</a></h3>';
  $term_link = 'Term link: <a href="' . $base_path . 'taxonomy/term/' . $tid . '">' . $term . '</a>';
  $term_content = 'Number of bone dysplasias sharing this characteristic: ' . $count;

  $scc_nids = publications_for_tid_bd($tid, $bd_nid);

  if (!empty($scc_nids)) {
    $term_content = $term_content . '<br/><strong>Publications:</strong><ul>';
    foreach ($scc_nids as $scc_nid) {
      $scc_node = node_load($scc_nid);
      foreach ($scc_node->field_ssc_biblio['und'] as $pub) {
	$pub_node = node_load($pub['nid']);

	$pub_html = '<li><a href="' . $base_path . 'node/' . $pub['nid'] . '">' . $pub_node->title . '</a>';
	if (user_is_logged_in()) {
	  $pub_html = $pub_html . ' [<a href="' . $base_path . 'node/' . $bd_nid . '/phenotype/citation/delete/' . $bd_nid . '/' . $tid . '/' . $scc_nid . '/' . $pub_node->nid . '">Delete</a>]</li>';
	} else {
	  $pub_html = $pub_html . '</li>';
	}

	$term_content = $term_content . $pub_html;
      }
      
    }

    $term_content = $term_content . '</ul>';
  }

  $add_citation_link = '<a id="add_citation_button_' . $idx . '" href="#">Add citation</a>';
  if (is_current_user_editor()) {
    $remove_link = '<a id="remove_button_' . $idx . '" href="#">Delete</a>';
    $term_html = $term_html . '<div>' . $term_link . '<p>' . $term_content . '</p><div style="text-align:right;">' . $add_citation_link . ' ' . $remove_link . '</div></div>';
  } else {
    if (user_is_logged_in()) {
      $term_html = $term_html . '<div>' . $term_link . '<p>' . $term_content . '</p><div style="text-align:right;">' . $add_citation_link . '</div></div>';
    } else {
      $term_html = $term_html . '<div>' . $term_link . '<p>' . $term_content . '</div>';
    }
  }

  return $term_html;
}

function publications_for_tid_bd($tid, $bd_nid) {
  $scc_pheno = get_scc_for_phenotid($tid);
  $scc_bd = get_scc_for_bd($bd_nid);
  
  return array_intersect($scc_pheno, $scc_bd);
}

function delete_phenotype_callback($node, $pheno_tid) {
  $to_keep = array();
  foreach ($node->field_skeletome_tags['und'] as $pheno) {
    if ($pheno['tid'] != $pheno_tid) {
      $to_keep[] = $pheno;
    }
  }

  $node->field_skeletome_tags['und'] = $to_keep;
  node_save($node);

  $scc_bds = get_scc_for_bd($node->nid);
  $scc_tids = get_scc_for_phenotid($pheno_tid);
  $sccs = array_intersect($scc_bds, $scc_tids);

  if (!empty($sccs)) {
    $scc_nid = $sccs[0];
    remove_from_array($node, 'field_bd_citationcontext', 'nid', $scc_nid, TRUE);

    $pheno_term = taxonomy_term_load($pheno_tid);
    remove_from_array($pheno_term, 'field_term_citationcontext', 'nid', $scc_nid, FALSE);

    $scc_node = node_load($scc_nid);
    foreach ($scc_node->field_ssc_biblio['und'] as $pub) {
      $biblio_node = node_load($pub['nid']);
      remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);
    }

    node_delete($scc_nid);
  }

  drupal_goto('node/' . $node->nid . '/phenotype');
}


function add_phenotype_callback($node, $pheno_tid) {
  if (array_key_exists('und', $node->field_skeletome_tags)) {
    $exists = FALSE;
    foreach ($node->field_skeletome_tags['und'] as $pheno) {
      if ($pheno['tid'] === $pheno_tid) {
	$exists = TRUE;
	break;
      }
    }
    if (!$exists) {
      $count = count($node->field_skeletome_tags['und']);
      $tids = array();
      $tids['tid'] = $pheno_tid;
      $node->field_skeletome_tags['und'][$count] = $tids;
      node_save($node);
    }
  } else {
    $tids = array();
    $tids['tid'] = $pheno_tid;
    $node->field_skeletome_tags['und'][0] = $tids;
    node_save($node);
  }

  drupal_goto('node/' . $node->nid . '/phenotype');
}

function add_citation_callback($node) {
  $phenoTid = $_POST['phenoTid'];
  $pubNid = $_POST['pubNid'];

  if (array_key_exists('und', $node->field_bd_citationcontext)) {
    $scc_nids = get_scc_for_phenotid($phenoTid);
    $existing_scc = NULL;

    foreach ($node->field_bd_citationcontext['und'] as $scc) {
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
      $scc_nid = create_scc($node->nid, $phenoTid, $pubNid);

      $count = count($node->field_bd_citationcontext['und']);
      $node->field_bd_citationcontext['und'][$count]['nid'] = $scc_nid;
      node_save($node);

      $pheno_term = taxonomy_term_load($phenoTid);
      if (array_key_exists('und', $pheno_term->field_term_citationcontext)) {
	$count = count($pheno_term->field_term_citationcontext['und']);
	$pheno_term->field_term_citationcontext['und'][$count]['nid'] = $scc_nid;
      } else {
	$pheno_term->field_term_citationcontext['und'][0]['nid'] = $scc_nid;
      }
      taxonomy_term_save($pheno_term);

      add_scc_to_biblio($pubNid, $scc_nid);
    }
  } else {
    $scc_nid = create_scc($node->nid, $phenoTid, $pubNid);
    $node->field_bd_citationcontext['und'][0]['nid'] = $scc_nid;
    node_save($node);

    $pheno_term = taxonomy_term_load($phenoTid);
    if (array_key_exists('und', $pheno_term->field_term_citationcontext)) {
      $count = count($pheno_term->field_term_citationcontext['und']);
      $pheno_term->field_term_citationcontext['und'][$count]['nid'] = $scc_nid;
    } else {
      $pheno_term->field_term_citationcontext['und'][0]['nid'] = $scc_nid;
    }
    taxonomy_term_save($pheno_term);

    add_scc_to_biblio($pubNid, $scc_nid);
  }

  drupal_goto('node/' . $node->nid . '/phenotype');
}

function delete_citation_callback($bd_nid, $tid, $scc_nid, $pub_nid) {
  $scc_node = node_load($scc_nid);

  $to_keep = array();
  foreach ($scc_node->field_ssc_biblio['und'] as $pub) {
    if ($pub['nid'] != $pub_nid) {
      $to_keep[] = $pub;
    }
  }

  if (!empty($to_keep)) {
    $scc_node->field_ssc_biblio['und'] = $to_keep;
    node_save($scc_node);
    
    $biblio_node = node_load($pub_nid);
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);
  } else {
    $biblio_node = node_load($pub_nid);
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);

    $pheno_term = taxonomy_term_load($tid);
    remove_from_array($pheno_term, 'field_term_citationcontext', 'nid', $scc_node->nid, FALSE);

    $bd_node = node_load($bd_nid);
    remove_from_array($bd_node, 'field_bd_citationcontext', 'nid', $scc_node->nid, TRUE);
    
    node_delete($scc_node->nid);
  } 

  drupal_goto('node/' . $bd_nid . '/phenotype');
}

function create_scc($bdNid, $phenoTid, $pubNid) {
  global $user;

  $scc_node = new stdClass();
  $scc_node->type = 'skeletome_citation_context';
  $scc_node->language = 'und';
  $scc_node->title = 'SCC';
  $scc_node->uid = $user->uid;
  $scc_node->field_ssc_bonedysplasia['und'][0]['nid'] = $bdNid;
  $scc_node->field_ssc_biblio['und'][0]['nid'] = $pubNid;
  $scc_node->field_ssc_phenoterm['und'][0]['tid'] = $phenoTid;
  node_save($scc_node);

  return $scc_node->nid;
}

