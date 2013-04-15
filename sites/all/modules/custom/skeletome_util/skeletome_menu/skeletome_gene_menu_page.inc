<?php

define('__ADD_GENE__', 'edit-configuration-add-gene');
define('__DELETE_GENE__', 'edit-configuration-delete-gene');
define('__CREATE_GENEMUTATION__', 'edit-configuration-create-genemutation');
define('__DELETE_GENEMUTATION__', 'edit-configuration-delete-genemutation');

function gene_menu_admin() {
  return drupal_get_form('gene_menu_form');
}

function gene_menu_form($form, &$form_state) {
  $form['configuration'] = array(
    '#type' => 'vertical_tabs',
    '#title' => t('Gene and gene mutations administration'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );

  $form['configuration']['add_gene'] = array(
     '#type' => 'fieldset',
      '#title' => t('Add new gene'),
      '#weight' => 1,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _add_gene_form(),
  );

  $form['configuration']['delete_gene'] = array(
     '#type' => 'fieldset',
      '#title' => t('Delete gene'),
      '#weight' => 2,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _delete_gene_form(),
  );

  $form['configuration']['create_genemutation'] = array(
     '#type' => 'fieldset',
      '#title' => t('Create gene mutation'),
      '#weight' => 3,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _create_genemutation_form(),
  );

  $form['configuration']['delete_genemutation'] = array(
     '#type' => 'fieldset',
      '#title' => t('Delete gene mutation'),
      '#weight' => 4,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
  );

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $selected = isset($form_state['values']['configuration']['delete_genemutation']['bd_list']) ? $form_state['values']['configuration']['delete_genemutation']['bd_list'] : key($options);

  $form['configuration']['delete_genemutation']['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia.'),
    '#options' => $options,
    '#ajax' => array(
      'callback' => '_get_bd_callback',
      'wrapper' => 'gm_list_div',
    ),
  );

  $form['configuration']['delete_genemutation']['gm_list'] = array(
    '#type' => 'select',
    '#title' => t('Gene Mutations'),
    '#description' => t('Select the Gene Mutation that should be removed.'),
    '#prefix' => '<div id="gm_list_div">',
    '#suffix' => '</div>',
    '#options' => _get_gm_options($selected),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function _get_bd_callback($form, $form_state) {
  return $form['configuration']['delete_genemutation']['gm_list'];
}

function _get_gm_options($selected) {
  $bd_node = node_load($selected);
  
  if (array_key_exists('und', $bd_node->field_bd_gm)) {
    $gm_nids = array();
    foreach ($bd_node->field_bd_gm['und'] as $gm) {
      $gm_nids[] = $gm['nid'];
    }
    $options = array();
    foreach($gm_nids as $gm_nid) {
      $gm_node = node_load($gm_nid);
      $options[$gm_nid] = $gm_node->title;
    }
    return $options;
  } else {
    return array();
  }
}

function _add_gene_form() {
  $add_gene_form = array();

  $add_gene_form['new_gene_name'] = array(
    '#type' => 'textfield',
    '#title' => t('New Gene name'),
    '#description' => t('Specify the name of the new Gene.'),
  );

  return $add_gene_form;
}

function _delete_gene_form() {
  $delete_gene_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'gene'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $delete_gene_form['gene_list'] = array (
    '#type' => 'select',
    '#title' => t('Genes'),
    '#description' => t('Select the Gene to be deleted.'),
    '#options' => $options,
  );

  return $delete_gene_form;
}

function _create_genemutation_form() {
  $create_gm_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'gene'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $create_gm_form['gene_list'] = array (
    '#type' => 'select',
    '#title' => t('Genes'),
    '#description' => t('Select the Gene.'),
    '#options' => $options,
  );

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $create_gm_form['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dyplasia to be linked to the new Gene Mutation.'),
    '#options' => $options,
  );

  return $create_gm_form;
}

function gene_menu_form_submit($form, &$form_state) {
  $active_tab = $form_state['input']['configuration']['configuration__active_tab'];
  if ($active_tab == __ADD_GENE__) {
    _add_gene($form, $form_state);
  }
  if ($active_tab == __DELETE_GENE__) {
    _delete_gene($form, $form_state);
  }
  if ($active_tab == __CREATE_GENEMUTATION__) {
    _create_genemutation($form, $form_state);
  }
  if ($active_tab == __DELETE_GENEMUTATION__) {
    _delete_genemutation($form, $form_state);
  }
}

function _add_gene($form, $form_state) {
  $add_gene_values = $form_state['values']['configuration']['add_gene']['value'];
  $new_gene_name = $add_gene_values['new_gene_name'];
  if (empty($new_gene_name)) {
    drupal_set_message('Please enter a valid Gene name.', 'error');
  } else {
    drupal_goto('node/add/gene/' . $new_gene_name);
  }
}

function _create_genemutation($form, $form_state) {
  $create_gm_values = $form_state['values']['configuration']['create_genemutation']['value'];
  $gene_nid = $create_gm_values['gene_list'];
  $bd_nid = $create_gm_values['bd_list'];
  
  drupal_goto('node/add/gene-mutation/' . $gene_nid . '/' . $bd_nid);
}

function _delete_gene($form, $form_state) {
  $delete_gene_values = $form_state['values']['configuration']['delete_gene']['value'];
  $current_gene = $delete_gene_values['gene_list'];
  drupal_goto('node/' . $current_gene . '/delete');
}

function _delete_genemutation($form, $form_state) {
  $gm_value = $form_state['values']['configuration']['delete_genemutation']['gm_list'];
  drupal_goto('node/' . $gm_value . '/delete');
}

