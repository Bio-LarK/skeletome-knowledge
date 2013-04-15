<?php

define('__ADD_BDG__', 'edit-configuration-add-group');
define('__MERGE_BDG__', 'edit-configuration-merge-groups');
define('__DELETE_BDG__', 'edit-configuration-delete-group');

function bdg_menu_admin() {
  return drupal_get_form('bdg_menu_form');
}

function bdg_menu_form() {
  $form['configuration'] = array(
    '#type' => 'vertical_tabs',
    '#title' => t('Bone Dyplasia groups administration'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );

  $form['configuration']['add_group'] = array(
     '#type' => 'fieldset',
      '#title' => t('Add new group'),
      '#weight' => 1,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _add_group_form(),
  );

  $form['configuration']['merge_groups'] = array(
     '#type' => 'fieldset',
      '#title' => t('Merge groups'),
      '#weight' => 3,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _merge_groups_form(),
  );

  $form['configuration']['delete_group'] = array(
     '#type' => 'fieldset',
      '#title' => t('Delete group'),
      '#weight' => 4,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      'value' => _delete_group_form(),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function _add_group_form() {
  $add_group_form = array();

  $add_group_form['new_bdg_name'] = array(
    '#type' => 'textfield',
    '#title' => t('New Bone Dysplasia group name'),
    '#description' => t('Specify the name of the new Bone Dysplasia group.'),
  );

  return $add_group_form;
}

function _merge_groups_form() {
  $merge_groups_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia_group'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $merge_groups_form['bd_groups_1'] = array (
    '#type' => 'select',
    '#title' => t('First Bone Dysplasia group'),
    '#description' => t('Select the first Bone Dysplasia group to be merged.'),
    '#options' => $options,
  );

  $merge_groups_form['bd_groups_2'] = array (
    '#type' => 'select',
    '#title' => t('Second Bone Dysplasia group'),
    '#description' => t('Select the second Bone Dysplasia group to be merged.'),
    '#options' => $options,
  );

  $merge_groups_form['new_bdg_name'] = array(
    '#type' => 'textfield',
    '#title' => t('New Bone Dysplasia group name'),
    '#description' => t('Specify the name of the newly merged Bone Dysplasia group.'),
  );

  return $merge_groups_form;
}

function _delete_group_form() {
  $delete_group_form = array();

  $options = array();
  $sql = 'SELECT nid, title FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'bone_dysplasia_group'));
  foreach ($result as $row) {
    $options[$row->nid] = $row->title;
  }

  $delete_group_form['bd_groups'] = array (
    '#type' => 'select',
    '#title' => t('Bone Dysplasia group'),
    '#description' => t('Select the Bone Dysplasia group to be deleted.'),
    '#options' => $options,
  );

  return $delete_group_form;
}

function bdg_menu_form_submit($form, &$form_state) {
  $active_tab = $form_state['input']['configuration']['configuration__active_tab'];
  if ($active_tab == __ADD_BDG__) {
    _add_bdg($form, $form_state);
  }
  if ($active_tab == __MERGE_BDG__) {
    _merge_bdg($form, $form_state);
  }
  if ($active_tab == __DELETE_BDG__) {
    _delete_bdg($form, $form_state);
  }
}

function _add_bdg($form, $form_state) {
  $add_bdg_values = $form_state['values']['configuration']['add_group']['value'];
  $bdg_new_name = $add_bdg_values['new_bdg_name'];
  if (empty($bdg_new_name)) {
    drupal_set_message('Please enter a valid Bone Dysplasia group name.', 'error');
  } else {
    drupal_goto('node/add/bone-dysplasia-group/' . $bdg_new_name);
  }
}

function _merge_bdg($form, $form_state) {
  $merge_bdg_values = $form_state['values']['configuration']['merge_groups']['value'];
  $bdg_current1 = $merge_bdg_values['bd_groups_1'];
  $bdg_current2 = $merge_bdg_values['bd_groups_2'];
  $bdg_new_name = $merge_bdg_values['new_bdg_name'];

  if (empty($bdg_new_name)) {
    drupal_set_message('Please enter a valid Bone Dysplasia group name.', 'error');
  } else {
    global $user;

    $new_bdg = new stdClass();
    $new_bdg->type = 'bone_dysplasia_group';
    $new_bdg->language = 'und';
    $new_bdg->title = $bdg_new_name;
    $new_bdg->uid = $user->uid; 
    node_save($new_bdg);

    $all_bd_count = 0;

    $bdg_1 = node_load($bdg_current1);
    if (array_key_exists('und', $bdg_1->field_bdgroup_bonedysplasia)) {
      if (!empty($bdg_1->field_bdgroup_bonedysplasia['und'])) {
	$bds = $bdg_1->field_bdgroup_bonedysplasia['und'];
	foreach ($bds as $bd) {
	  $new_bdg->field_bdgroup_bonedysplasia['und'][$all_bd_count++]['nid'] = $bd['nid'];
	  $bd_node = node_load($bd['nid']);
	  
	  if (array_key_exists('und', $bd_node->field_bd_group)) {
	    if (!empty($bd_node->field_bd_group['und'])) {
	      $groups = $bd_node->field_bd_group['und'];
	      $group_idx = -1;
	      $count = 0;
	      foreach ($groups as $group) {
		if ($group['nid'] == $bdg_1->nid) {
		  $group_idx = $count;
		  break;
		}
		$count++;
	      }
	      if ($group_idx != -1) {
		array_splice($groups, $group_idx, 1);
		$groups[count($groups)]['nid'] = $new_bdg->nid;
		$bd_node->field_bd_group['und'] = $groups;
		node_save($bd_node);
	      }
	    }
	  }
	}
	$bdg_1->field_bdgroup_bonedysplasia['und'] = array();
	node_save($bdg_1);
	node_delete($bdg_1->nid);
      }
    }

    $bdg_2 = node_load($bdg_current2);
    if (array_key_exists('und', $bdg_2->field_bdgroup_bonedysplasia)) {
      if (!empty($bdg_2->field_bdgroup_bonedysplasia['und'])) {
	$bds = $bdg_2->field_bdgroup_bonedysplasia['und'];
	foreach ($bds as $bd) {
	  $new_bdg->field_bdgroup_bonedysplasia['und'][$all_bd_count++]['nid'] = $bd['nid'];
	  $bd_node = node_load($bd['nid']);
	  
	  if (array_key_exists('und', $bd_node->field_bd_group)) {
	    if (!empty($bd_node->field_bd_group['und'])) {
	      $groups = $bd_node->field_bd_group['und'];
	      $group_idx = -1;
	      $count = 0;
	      foreach ($groups as $group) {
		if ($group['nid'] == $bdg_2->nid) {
		  $group_idx = $count;
		  break;
		}
		$count++;
	      }
	      if ($group_idx != -1) {
		array_splice($groups, $group_idx, 1);
		$groups[count($groups)]['nid'] = $new_bdg->nid;
		$bd_node->field_bd_group['und'] = $groups;
		node_save($bd_node);
	      }
	    }
	  }
	}
	$bdg_2->field_bdgroup_bonedysplasia['und'] = array();
	node_save($bdg_2);
	node_delete($bdg_2->nid);
      }
    }

    node_save($new_bdg);

    drupal_set_message('Merge operation successfully performed.');
  }
}

function _delete_bdg($form, $form_state) {
  $delete_bdg_values = $form_state['values']['configuration']['delete_group']['value'];
  $bdg_current = $delete_bdg_values['bd_groups'];
  drupal_goto('node/' . $bdg_current . '/delete');
}
