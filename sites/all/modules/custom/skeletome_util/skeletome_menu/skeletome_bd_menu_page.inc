<?php

define('__ADD_BD__', 'edit-configuration-add-bd');
define('__ADD_TO_GROUP_BD__', 'edit-configuration-addtogroup-bd');
define('__REMOVE_FROM_GROUP_BD__', 'edit-configuration-removefromgroup-bd');
define('__DEPRECATE_BD__', 'edit-configuration-deprecate-bd');
define('__PROMOTE_BD__', 'edit-configuration-promote-bd');
define('__EQUIVALENT_BD__', 'edit-configuration-add-equivalent');
define('__REMOVE_EQUIVALENT_BD__', 'edit-configuration-remove-equivalent');
define('__DELETE_BD__', 'edit-configuration-delete-bd');

function bd_menu_admin() {
  return drupal_get_form('bd_menu_form');
}

function bd_menu_form($form, &$form_state) {
  $form['configuration'] = array(
    '#type' => 'vertical_tabs',
    '#title' => t('Bone Dyplasias administration'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );

  $form['configuration']['add_bd'] = array(
     '#type' => 'fieldset',
      '#title' => t('Add new Bone Dysplasia'),
      '#weight' => 1,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _add_bd_form(),
  );

  $form['configuration']['addtogroup_bd'] = array(
     '#type' => 'fieldset',
      '#title' => t('Add Bone Dysplasia to group'),
      '#weight' => 2,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _addtogroup_bd_form(),
  );

  $form['configuration']['removefromgroup_bd'] = array(
     '#type' => 'fieldset',
      '#title' => t('Remove Bone Dysplasia from group'),
      '#weight' => 3,
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

  $selected = isset($form_state['values']['configuration']['removefromgroup_bd']['bd_list']) ? $form_state['values']['configuration']['removefromgroup_bd']['bd_list'] : key($options);

  $form['configuration']['removefromgroup_bd']['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia.'),
    '#options' => $options,
    '#ajax' => array(
      'callback' => '_get_bdgroups_callback',
      'wrapper' => 'bd_groups',
    ),
  );

  $form['configuration']['removefromgroup_bd']['bd_groups'] = array(
    '#type' => 'select',
    '#title' => t('Bone Dysplasia group'),
    '#description' => t('Select the Bone Dysplasia group from which the selected Bone Dysplasia should be removed.'),
    '#prefix' => '<div id="bd_groups">',
    '#suffix' => '</div>',
    '#options' => _get_bdgroups_options($selected),
  );

  $form['configuration']['deprecate_bd'] = array(
     '#type' => 'fieldset',
      '#title' => t('Deprecate Bone Dyplasia'),
      '#weight' => 4,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _deprecate_bd_form(),
  );

  $form['configuration']['promote_bd'] = array(
     '#type' => 'fieldset',
      '#title' => t('Promote deprecated Bone Dyplasia'),
      '#weight' => 5,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _promote_bd_form(),
  );

  $form['configuration']['add_equivalent'] = array(
     '#type' => 'fieldset',
      '#title' => t('Add equivalent Bone Dyplasia'),
      '#weight' => 6,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _add_equivalent_bd_form(),
  );

  $form['configuration']['remove_equivalent'] = array(
     '#type' => 'fieldset',
      '#title' => t('Remove equivalent Bone Dyplasia'),
      '#weight' => 7,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
  );

  $sameas_options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $bd_node = node_load($row->nid);
    if (array_key_exists('und', $bd_node->field_bd_sameas)) {
      if (!empty($bd_node->field_bd_sameas['und'])) {
	$sameas_options[$row->nid] = $row->title;
      }
    }
  }

  $sameas_selected = isset($form_state['values']['configuration']['remove_equivalent']['bd_list']) ? $form_state['values']['configuration']['remove_equivalent']['bd_list'] : key($sameas_options);

  $form['configuration']['remove_equivalent']['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia.'),
    '#options' => $sameas_options,
    '#ajax' => array(
      'callback' => '_get_sameas_callback',
      'wrapper' => 'bd_sameas',
    ),
  );

  $form['configuration']['remove_equivalent']['bd_list_equiv'] = array(
    '#type' => 'select',
    '#title' => t('Bone Dysplasia group'),
    '#description' => t('Select the Bone Dysplasia group from which the selected Bone Dysplasia should be removed.'),
    '#prefix' => '<div id="bd_sameas">',
    '#suffix' => '</div>',
    '#options' => _get_sameas_options($sameas_selected),
  );

  $form['configuration']['delete_bd'] = array(
     '#type' => 'fieldset',
      '#title' => t('Delete Bone Dyplasia'),
      '#weight' => 8,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _delete_bd_form(),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function _add_bd_form() {
  $add_bd_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia_group'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $add_bd_form['bd_groups'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia group'),
    '#description' => t('Select the Bone Dysplasia group to contain the new Bone Dysplasia.'),
    '#options' => $options,
  );

  $add_bd_form['new_bd_name'] = array(
    '#type' => 'textfield',
    '#title' => t('New Bone Dysplasia name'),
    '#description' => t('Specify the name of the new Bone Dysplasia.'),
  );

  return $add_bd_form;
}

function _addtogroup_bd_form() {
  $add_to_group_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $add_to_group_form['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia to be added to a group.'),
    '#options' => $options,
  );

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia_group'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $add_to_group_form['bd_groups'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia group'),
    '#description' => t('Select the additional Bone Dysplasia group for the selected Bone Dysplasia.'),
    '#options' => $options,
  );

  return $add_to_group_form;
}

function _get_bdgroups_callback($form, $form_state) {
  return $form['configuration']['removefromgroup_bd']['bd_groups'];
}

function _get_sameas_callback($form, $form_state) {
  return $form['configuration']['remove_equivalent']['bd_list_equiv'];
}

function _get_bdgroups_options($selected) {
  $bd_node = node_load($selected);
  
  if (array_key_exists('und', $bd_node->field_bd_group)) {
    $bdgroup_nids = array();
    foreach ($bd_node->field_bd_group['und'] as $bdgroup) {
      $bdgroup_nids[] = $bdgroup['nid'];
    }
    $options = array();
    foreach($bdgroup_nids as $bdgroup_nid) {
      $bdgroup_node = node_load($bdgroup_nid);
      $options[$bdgroup_nid] = $bdgroup_node->title;
    }
    return $options;
  } else {
    return array();
  }
}

function _get_sameas_options($selected) {
  $bd_node = node_load($selected);
  
  if (array_key_exists('und', $bd_node->field_bd_sameas)) {
    $sameas_nids = array();
    foreach ($bd_node->field_bd_sameas['und'] as $sameas) {
      $sameas_nids[] = $sameas['nid'];
    }
    $options = array();
    foreach($sameas_nids as $sameas_nid) {
      $sameas_node = node_load($sameas_nid);
      $options[$sameas_nid] = $sameas_node->title;
    }
    return $options;
  } else {
    return array();
  }
}

function _deprecate_bd_form() {
  $deprecate_bd_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $deprecate_bd_form['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia to be deprecated.'),
    '#options' => $options,
  );

  $deprecate_bd_form['bd_list_parent'] = array (
    '#type' => 'select',
    '#title' => t('New parent Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia to be considered as a parent for the deprecated Bone Dysplasia.'),
    '#options' => $options,
  );

  return $deprecate_bd_form;
}

function _promote_bd_form() {
  $promote_bd_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $bd_node = node_load($row->nid);
    if (array_key_exists('und', $bd_node->field_bd_superbd)) {
      if (!empty($bd_node->field_bd_superbd['und'])) {
	$options[$row->nid] = $row->title;
      }
    }
  }

  $promote_bd_form['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia to be promoted.'),
    '#options' => $options,
  );

  return $promote_bd_form;
}

function _add_equivalent_bd_form() {
  $add_equivalent_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $add_equivalent_form['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia for which you want to add an equivalent.'),
    '#options' => $options,
  );

  $add_equivalent_form['bd_list_equiv'] = array (
    '#type' => 'select',
    '#title' => t('New equivalent Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia to be considered as an equivalent.'),
    '#options' => $options,
  );

  return $add_equivalent_form;
}

function _delete_bd_form() {
  $delete_bd_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $delete_bd_form['bd_list'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia'),
    '#description' => t('Select the Bone Dysplasia to be deleted.'),
    '#options' => $options,
  );

  return $delete_bd_form;
}

function bd_menu_form_submit($form, &$form_state) {
  $active_tab = $form_state['input']['configuration']['configuration__active_tab'];

  if ($active_tab == __ADD_BD__) {
    _add_bd($form, $form_state);
  }
  if ($active_tab == __ADD_TO_GROUP_BD__) {
    _add_to_group_bd($form, $form_state);
  }
  if ($active_tab == __REMOVE_FROM_GROUP_BD__) {
    _remove_from_group_bd($form, $form_state);
  }
  if ($active_tab == __DEPRECATE_BD__) {
    _deprecate_bd($form, $form_state);
  }
  if ($active_tab == __PROMOTE_BD__) {
    _promote_bd($form, $form_state);
  }
  if ($active_tab == __EQUIVALENT_BD__) {
    _add_equivalent_bd($form, $form_state);
  }
  if ($active_tab == __REMOVE_EQUIVALENT_BD__) {
    _remove_equivalent_bd($form, $form_state);
  }
  if ($active_tab == __DELETE_BD__) {
    _delete_bd($form, $form_state);
  }
}

function _add_bd($form, $form_state) {
  $add_bd_values = $form_state['values']['configuration']['add_bd']['value'];
  $bd_new_name = $add_bd_values['new_bd_name'];
  $bd_group = $add_bd_values['bd_groups'];
  if (empty($bd_new_name)) {
    drupal_set_message('Please enter a valid Bone Dysplasia name.', 'error');
  } else {
    drupal_goto('node/add/bone-dysplasia/' . $bd_new_name . '/' . $bd_group);
  }
}

function _add_to_group_bd($form, $form_state) {
  $bd_value = $form_state['values']['configuration']['addtogroup_bd']['value']['bd_list'];
  $bdgroup_value = $form_state['values']['configuration']['addtogroup_bd']['value']['bd_groups'];

  $bd_node = node_load($bd_value);
  if (array_key_exists('und', $bd_node->field_bd_group)) {
    $found = FALSE;
    foreach ($bd_node->field_bd_group['und'] as $bdgroup) {
      if ($bdgroup['nid'] == $bdgroup_value) {
	$found = TRUE;
	break;
      }
    }
    if (!$found) {
      $count = count($bd_node->field_bd_group['und']);
      $bd_node->field_bd_group['und'][$count]['nid'] = $bdgroup_value;

      $bdgroup_node = node_load($bdgroup_value);
      if (array_key_exists('und', $bdgroup_node->field_bdgroup_bonedysplasia)) {
	$count = count($bdgroup_node->field_bdgroup_bonedysplasia['und']);
	$bdgroup_node->field_bdgroup_bonedysplasia['und'][$count]['nid'] = $bd_value;
      } else {
	$bdgroup_node->field_bdgroup_bonedysplasia['und'][0]['nid'] = $bd_value;
      }

      node_save($bd_node);
      node_save($bdgroup_node);
      drupal_set_message('Operation successfully performed.');
    } else {
      drupal_set_message('The selected Bone Dysplasia is already part of that group.', 'error');
    }

  } else {
    $bd_node->field_bd_group['und'][0]['nid'] = $bdgroup_value;
    $bdgroup_node = node_load($bdgroup_value);
    if (array_key_exists('und', $bdgroup_node->field_bdgroup_bonedysplasia)) {
      $count = count($bdgroup_node->field_bdgroup_bonedysplasia['und']);
      $bdgroup_node->field_bdgroup_bonedysplasia['und'][$count]['nid'] = $bd_value;
    } else {
      $bdgroup_node->field_bdgroup_bonedysplasia['und'][0]['nid'] = $bd_value;
    }

    node_save($bd_node);
    node_save($bdgroup_node);
    drupal_set_message('Operation successfully performed.');
  }

}

function _remove_from_group_bd($form, $form_state) {
  $bd_value = $form_state['values']['configuration']['removefromgroup_bd']['bd_list'];
  $bdgroup_value = $form_state['values']['configuration']['removefromgroup_bd']['bd_groups'];

  $bd_node = node_load($bd_value);
  $groups = $bd_node->field_bd_group['und'];
  $group_idx = -1;
  $count = 0;
  foreach ($groups as $group) {
    if ($group['nid'] == $bdgroup_value) {
      $group_idx = $count;
      break;
    }
    $count++;
  }
  if ($group_idx != -1) {
    array_splice($groups, $group_idx, 1);
    $bd_node->field_bd_group['und'] = $groups;
    node_save($bd_node);
  }

  $bdgroup_node = node_load($bdgroup_value);
  $bds = $bdgroup_node->field_bdgroup_bonedysplasia['und'];
  $bd_idx = -1;
  $count = 0;
  foreach ($bds as $bd) {
    if ($bd['nid'] == $bd_value) {
      $bd_idx = $count;
      break;
    }
    $count++;
  }
  if ($bd_idx != -1) {
    array_splice($bds, $bd_idx, 1);
    $bdgroup_node->field_bdgroup_bonedysplasia['und'] = $bds;
    node_save($bdgroup_node);
  }

  drupal_set_message('Operation successfully performed.');
}

function _deprecate_bd($form, $form_state) {
  $deprecate_bd_values = $form_state['values']['configuration']['deprecate_bd']['value'];
  $bd_parent = $deprecate_bd_values['bd_list_parent'];
  $bd_value = $deprecate_bd_values['bd_list'];

  $bd_node = node_load($bd_value);

  if (array_key_exists('und', $bd_node->field_bd_superbd)) {
    if (!empty($bd_node->field_bd_superbd['und'])) {
      drupal_set_message('The selected Bone Dysplasia already has a super type.', 'error');
      return;
    }
  }

  if (array_key_exists('und', $bd_node->field_bd_group)) {
    foreach ($bd_node->field_bd_group['und'] as $group) {
      _remove_bd_from_group($bd_value, $group['nid']);
    }
    $bd_node->field_bd_group['und'] = array();
  }

  $bd_parent_node = node_load($bd_parent);
  if (array_key_exists('und', $bd_parent_node->field_bd_group)) {
    $count = 0;
    foreach ($bd_parent_node->field_bd_group['und'] as $group) {
      $bd_node->field_bd_group['und'][$count++]['nid'] = $group['nid'];
      _add_bd_to_group($bd_value, $group['nid']);
    }
  }
  $bd_node->field_bd_superbd['und'][0]['nid'] = $bd_parent;

  if (array_key_exists('und', $bd_parent_node->field_bd_subbd)) {
    $count = count($bd_parent_node->field_bd_subbd['und']);
    $bd_parent_node->field_bd_subbd['und'][$count]['nid'] = $bd_value;
  } else {
    $bd_parent_node->field_bd_subbd['und'][0]['nid'] = $bd_value;
  }

  node_save($bd_node);
  node_save($bd_parent_node);
  drupal_set_message('Operation successfully performed.');
}

function _add_bd_to_group($bd_value, $group) {
  $bdgroup_node = node_load($group);
  if (array_key_exists('und', $bdgroup_node->field_bdgroup_bonedysplasia)) {
    $count = count($bdgroup_node->field_bdgroup_bonedysplasia['und']);
    $bdgroup_node->field_bdgroup_bonedysplasia['und'][$count]['nid'] = $bd_value;
  } else {
    $bdgroup_node->field_bdgroup_bonedysplasia['und'][0]['nid'] = $bd_value;
  }

  node_save($bdgroup_node);
}

function _remove_bd_from_group($bd_value, $group) {
  $bdgroup_node = node_load($group);
  $bds = $bdgroup_node->field_bdgroup_bonedysplasia['und'];
  $bd_idx = -1;
  $count = 0;
  foreach ($bds as $bd) {
    if ($bd['nid'] == $bd_value) {
      $bd_idx = $count;
      break;
    }
    $count++;
  }
  if ($bd_idx != -1) {
    array_splice($bds, $bd_idx, 1);
    $bdgroup_node->field_bdgroup_bonedysplasia['und'] = $bds;
    node_save($bdgroup_node);
  }
}

function _promote_bd($form, $form_state) {
  $deprecate_bd_values = $form_state['values']['configuration']['promote_bd']['value'];
  $bd_value = $deprecate_bd_values['bd_list'];

  $bd_node = node_load($bd_value);
  $bd_parent = $bd_node->field_bd_superbd['und'][0]['nid'];
  $bd_node->field_bd_superbd['und'] = array();

  $bd_parent_node = node_load($bd_parent);
  $count = 0;
  $bd_idx = -1;
  foreach ($bd_parent_node->field_bd_subbd['und'] as $subtype) {
    if ($subtype['nid'] == $bd_value) {
      $bd_idx = $count;
      break;
    }
    $count++;
  }
  if ($bd_idx != -1) {
    $subtypes = $bd_parent_node->field_bd_subbd['und'];
    array_splice($subtypes, $bd_idx, 1);
    $bd_parent_node->field_bd_subbd['und'] = $subtypes;
  }

  node_save($bd_node);
  node_save($bd_parent_node);
  drupal_set_message('Operation successfully performed.');
}

function _add_equivalent_bd($form, $form_state) {
  $bd_value = $form_state['values']['configuration']['add_equivalent']['value']['bd_list'];
  $bdequiv_value = $form_state['values']['configuration']['add_equivalent']['value']['bd_list_equiv'];

  $bd_node = node_load($bd_value);
  if (array_key_exists('und', $bd_node->field_bd_sameas)) {
    $found = FALSE;
    foreach ($bd_node->field_bd_sameas['und'] as $bd_sameas) {
      if ($bd_sameas['nid'] == $bdequiv_value) {
	$found = TRUE;
	break;
      }
    }
    if (!$found) {
      $count = count($bd_node->field_bd_sameas['und']);
      $bd_node->field_bd_sameas['und'][$count]['nid'] = $bdequiv_value;

      $bd_equiv_node = node_load($bdequiv_value);
      if (array_key_exists('und', $bd_equiv_node->field_bd_sameas)) {
	$count = count($bd_equiv_node->field_bd_sameas['und']);
	$bd_equiv_node->field_bd_sameas['und'][$count]['nid'] = $bd_value;
      } else {
	$bd_equiv_node->field_bd_sameas['und'][0]['nid'] = $bd_value;
      }

      node_save($bd_node);
      node_save($bd_equiv_node);
      drupal_set_message('Operation successfully performed.');
    } else {
      drupal_set_message('The selected Bone Dysplasia for equivalency is already equivalent with the Bone Dysplasia.', 'error');
    }
  } else {
    $bd_node->field_bd_sameas['und'][0]['nid'] = $bdequiv_value;
    $bd_equiv_node = node_load($bdequiv_value);

    if (array_key_exists('und', $bd_equiv_node->field_bd_sameas)) {
      $count = count($bd_equiv_node->field_bd_sameas['und']);
      $bd_equiv_node->field_bd_sameas['und'][$count]['nid'] = $bd_value;
    } else {
      $bd_equiv_node->field_bd_sameas['und'][0]['nid'] = $bd_value;
    }

    node_save($bd_node);
    node_save($bd_equiv_node);
    drupal_set_message('Operation successfully performed.');
  }
}

function _remove_equivalent_bd($form, $form_state) {
  $bd_value = $form_state['values']['configuration']['remove_equivalent']['bd_list'];
  $bdequiv_value = $form_state['values']['configuration']['remove_equivalent']['bd_list_equiv'];

  $bd_node = node_load($bd_value);
  $bd_equiv_node = node_load($bdequiv_value);

  $count = 0;
  $bd_equiv_idx = -1;
  $equivs = $bd_node->field_bd_sameas['und'];
  foreach ($equivs as $equiv) {
    if ($equiv['nid'] == $bdequiv_value) {
      $bd_equiv_idx = $count;
      break;
    }
    $count++;
  }
  if ($bd_equiv_idx != -1) {
    array_splice($equivs, $bd_equiv_idx, 1);
    $bd_node->field_bd_sameas['und'] = $equivs;
  }

  $count = 0;
  $bd_equiv_idx = -1;
  $equivs = $bd_equiv_node->field_bd_sameas['und'];
  foreach ($equivs as $equiv) {
    if ($equiv['nid'] == $bd_value) {
      $bd_equiv_idx = $count;
      break;
    }
    $count++;
  }
  if ($bd_equiv_idx != -1) {
    array_splice($equivs, $bd_equiv_idx, 1);
    $bd_equiv_node->field_bd_sameas['und'] = $equivs;
  }

  node_save($bd_node);
  node_save($bd_equiv_node);
  drupal_set_message('Operation successfully performed.');
}

function _delete_bd($form, $form_state) {
  $delete_bd_values = $form_state['values']['configuration']['delete_bd']['value'];
  drupal_goto('node/' . $delete_bd_values['bd_list'] . '/delete');
}

