<?php

function biblio_citations_callback($node) {
  $form_state['build_info']['args'] = array($node);
  return drupal_build_form('biblio_citations_form', $form_state);
}

//TODO: IMPLEMENT

function biblio_citations_form($node, &$form_state) {
  $node = $form_state['build_info']['args'][0];
  $form = NULL;
  $scc_gms = array();
  $scc_phenos = array();

  drupal_add_library('system', 'ui.accordion');
  drupal_add_library('system', 'ui.button');
  drupal_add_js(drupal_get_path('module', 'skeletome_util') .'/biblio_citations.js', array('group' => JS_DEFAULT));

  foreach($node->field_biblio_ssc_field['und'] as $scc) {
    $scc_node = node_load($scc['nid']);
    if (array_key_exists('und', $scc_node->field_ssc_genemutation)) {
      $scc_gms[] = $scc_node;
    }
    if (array_key_exists('und', $scc_node->field_ssc_phenoterm)) {
      $scc_phenos[] = $scc_node;
    }
  }

  if (!empty($scc_gms)) {
    $form['gm_title'] = array(
      '#markup' => '<div class="block-title"><h2 class="title">Gene Mutations</h2></div>',
      '#weight' => 1
    );

    $html_content = create_gm_accordion($scc_gms, $node->nid);

    $form['gm_accordion'] = array(
      '#markup' => $html_content,
      '#weight' => 2
    );

    $form['gm_line'] = array(
      '#markup' => '<hr style="border:dashed #CCCCCC; border-width:1px 0 0; height:0;" />',
      '#weight' => 3
    );
  }

  if (!empty($scc_phenos)) {
    $form['pheno_title'] = array(
      '#markup' => '<div class="block-title"><h2 class="title">Phenotypic characteristics</h2></div>',
      '#weight' => 4
    );

    $html_content = create_pheno_accordion($scc_phenos, $node->nid);

    $form['pheno_accordion'] = array(
      '#markup' => $html_content,
      '#weight' => 5
    );

    $form['pheno_line'] = array(
      '#markup' => '<hr style="border:dashed #CCCCCC; border-width:1px 0 0; height:0;" />',
      '#weight' => 6
    );
  }

  return $form;
}

function create_gm_accordion($scc_gms, $biblio_nid) {
  $html_content = '<div class="gm_accordion">';

  $gms = array();
  $count = 0;
  foreach($scc_gms as $scc_node) {
    $gm_nid = $scc_node->field_ssc_genemutation['und'][0]['nid'];
    $gms[$count] = $gm_nid; 

    $html_scc = create_html_for_gm_scc($count, $scc_node, $gm_nid, $biblio_nid);
    $count++;
    $html_content = $html_content . $html_scc;
  }
  drupal_add_js(array('gms' => $gms), 'setting');

  $html_content = $html_content . '</div>';
  return $html_content;
}

function create_html_for_gm_scc($idx, $scc_node, $gm_nid, $biblio_nid) {
  global $base_url;
  $base_path = url(NULL, array('absolute' => TRUE));
  $gm_node = node_load($gm_nid);
  $bd_nid = $scc_node->field_ssc_bonedysplasia['und'][0]['nid'];
  $bd_node = node_load($bd_nid);

  $scc_html = '<h3><a href="#">' . $gm_node->title . '</a></h3>';
  $delete_link = '<a id="delete_gm_button_' . $idx . '" href="' . $base_url . '/node/' . $biblio_nid . '/biblio/gm_citation/delete/' . $scc_node->nid . '/' . $biblio_nid . '">Delete</a>';

  $gm_link = 'Gene mutation link: <a href="' . $base_path . 'node/' . $gm_node->nid . '">' . $gm_node->title . '</a>';
  $bd_context = 'Bone dysplasia context: <a href="' . $base_path . 'node/' . $bd_nid . '">' . $bd_node->title . '</a>';

  $content = '<p>' . $gm_link . '<br />';
  $content = $content . $bd_context;
  $content = $content . '</p>';

  if (user_is_logged_in()) {
    $scc_html = $scc_html . '<div>' . $content . '<div style="text-align:right;">' . $delete_link . '</div></div>';
  } else {
    $scc_html = $scc_html . '<div>' . $content . '</div>';
  }


  return $scc_html;
}

function create_pheno_accordion($scc_phenos, $biblio_nid) {
  $html_content = '<div class="pheno_accordion">';

  $phenos = array();
  $count = 0;
  foreach($scc_phenos as $scc_node) {
    $pheno_tid = $scc_node->field_ssc_phenoterm['und'][0]['tid'];
    $phenos[$count] = $pheno_tid; 

    $html_scc = create_html_for_pheno_scc($count, $scc_node, $pheno_tid, $biblio_nid);
    $count++;
    $html_content = $html_content . $html_scc;
  }
  drupal_add_js(array('phenos' => $phenos), 'setting');

  $html_content = $html_content . '</div>';
  return $html_content;
}

function create_html_for_pheno_scc($idx, $scc_node, $pheno_tid, $biblio_nid) {
  global $base_url;
  $base_path = url(NULL, array('absolute' => TRUE));
  $pheno_term = taxonomy_term_load($pheno_tid);
  $bd_nid = $scc_node->field_ssc_bonedysplasia['und'][0]['nid'];
  $bd_node = node_load($bd_nid);

  $scc_html = '<h3><a href="#">' . $pheno_term->name . '</a></h3>';
  $delete_link = '<a id="delete_pheno_button_' . $idx . '" href="' . $base_url . '/node/' . $biblio_nid . '/biblio/pheno_citation/delete/' . $bd_nid . '/' . $pheno_tid . '/' . $scc_node->nid . '/' . $biblio_nid . '">Delete</a>';
  $term_link = 'Term link: <a href="' . $base_path . 'taxonomy/term/' . $pheno_tid . '">' . $pheno_term->name . '</a>';
  $bd_context = 'Bone dysplasia context: <a href="' . $base_path . 'node/' . $bd_nid . '">' . $bd_node->title . '</a>';

  $content = '<p>' . $term_link . '<br />';
  $content = $content . $bd_context;
  $content = $content . '</p>';

  if (user_is_logged_in()) {
    $scc_html = $scc_html . '<div>' . $content . '<div style="text-align:right;">' . $delete_link . '</div></div>';
  } else {
    $scc_html = $scc_html . '<div>' . $content . '</div>';
  }

  return $scc_html;
}

function delete_pheno_biblio_citation_callback($bd_nid, $tid, $scc_nid, $pub_nid) {
  $scc_node = node_load($scc_nid);
  $to_keep = array();
  foreach ($scc_node->field_ssc_biblio['und'] as $pub) {
    if ($pub['nid'] !== $pub_nid) {
      $to_keep[] = $pub;
    }
  }

  $biblio_node = node_load($pub_nid);
  if (!empty($to_keep)) {
    $scc_node->field_ssc_biblio['und'] = $to_keep;
    node_save($scc_node);
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);
  } else {
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);

    $pheno_term = taxonomy_term_load($tid);
    remove_from_array($pheno_term, 'field_term_citationcontext', 'nid', $scc_node->nid, FALSE);

    $bd_node = node_load($bd_nid);
    remove_from_array($bd_node, 'field_bd_citationcontext', 'nid', $scc_node->nid, TRUE);
    
    node_delete($scc_node->nid);
  } 

  if (array_key_exists('und', $biblio_node->field_biblio_ssc_field)) {
    if (!empty($biblio_node->field_biblio_ssc_field['und'])) {
      drupal_goto('node/' . $pub_nid . '/citations');
    } else {
      drupal_goto('node/' . $pub_nid);
    }
  } else {
    drupal_goto('node/' . $pub_nid);
  }
}

function delete_gm_biblio_citation_callback($scc_nid, $biblio_nid) {
  $scc_node = node_load($scc_nid);
  $gm_nid = $scc_node->field_ssc_genemutation['und'][0]['nid'];

  $to_keep = array();
  foreach ($scc_node->field_ssc_biblio['und'] as $pub) {
    if ($pub['nid'] != $biblio_nid) {
      $to_keep[] = $pub;
    }
  }

  $biblio_node = node_load($biblio_nid);
  if (!empty($to_keep)) {
    $scc_node->field_ssc_biblio['und'] = $to_keep;
    node_save($scc_node);
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);
  } else {
    remove_from_array($biblio_node, 'field_biblio_ssc_field', 'nid', $scc_node->nid, TRUE);

    $gm_node = node_load($gm_nid);
    remove_from_array($gm_node, 'field_gene_mutation_ssc', 'nid', $scc_node->nid, TRUE);

    $bd_node = node_load($scc_node->field_ssc_bonedysplasia['und'][0]['nid']);
    remove_from_array($bd_node, 'field_bd_citationcontext', 'nid', $scc_node->nid, TRUE);
    
    node_delete($scc_node->nid);
  }

  if (array_key_exists('und', $biblio_node->field_biblio_ssc_field)) {
    if (!empty($biblio_node->field_biblio_ssc_field['und'])) {
      drupal_goto('node/' . $biblio_nid . '/citations');
    } else {
      drupal_goto('node/' . $biblio_nid);
    }
  } else {
    drupal_goto('node/' . $biblio_nid);
  }
}