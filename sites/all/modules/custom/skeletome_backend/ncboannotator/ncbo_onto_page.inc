<?php

define('__ONTO_ID__', 'id');
define('__LOCALONTO_ID__', 'localOntologyId');
define('__ONTO_NAME__', 'name');
define('__ONTO_STATUS__', 'status');
define('__VIRTUALONTO_ID__', 'virtualOntologyId');

function ncboannotator_onto() {
  return drupal_get_form('ncboannotator_onto_form');
}

function ncboannotator_onto_form() {
  $ontologies = array();
  $options = array();
  $cached_ontologies = variable_get('NCBO_CACHED_ONTOLOGIES');

  if ($cached_ontologies != NULL) {
    $ontologies = $cached_ontologies;
    foreach ($ontologies as $ontology) {
      $options[$ontology[__ONTO_ID__]] = $ontology[__ONTO_NAME__];
    }
  } else {
    $annotator = new NCBOAnnotator();
    $onto_list_res = $annotator->retrieve_ontology_list();
    
    $code = $onto_list_res->code;
    $data = $onto_list_res->data;

    $xmlObj = simplexml_load_string($data);
    $arrObjData = get_object_vars($xmlObj);
    
    if ($code != 200) {
      drupal_set_message('Unable to retrieve the ontologies list. Please make sure the connection settings are correct.' , 'error');
      drupal_set_message($arrObjData['shortMessage'] . ': ' . $arrObjData['longMessage'] , 'error');
      variable_set('NCBO_ACTIVE', FALSE);
    } else {
      $object_array = get_object_vars($xmlObj->data->list);
      $count = 0;
      foreach ($object_array['ontologyBean'] as $onto_object) {
	$onto_object_array = get_object_vars($onto_object);
	
	if ($onto_object_array[__ONTO_STATUS__] == variable_get('NCBO_ONTOSTATUSCODE')) {
	  $ontology = array(
	    __ONTO_ID__		=> $onto_object_array[__ONTO_ID__],
	    __LOCALONTO_ID__		=> $onto_object_array[__LOCALONTO_ID__],
	    __ONTO_NAME__		=> $onto_object_array[__ONTO_NAME__],
	    __ONTO_STATUS__		=> $onto_object_array[__ONTO_STATUS__],
	    __VIRTUALONTO_ID__	=> $onto_object_array[__VIRTUALONTO_ID__],
	  );

	  $options[$onto_object_array[__ONTO_ID__]] = $onto_object_array[__ONTO_NAME__];
	  $ontologies[$count++] = $ontology;
	}
      }
      variable_set('NCBO_CACHED_ONTOLOGIES', $ontologies);
    }
  }

  if (!empty($ontologies)) {
    $form['isvirtualontoid'] = array(
      '#type' => 'select',
      '#title' => t('Is Virtual Ontology ID'),
      '#description' => t('Specifies whether the input values are virtualOntologyId instead of localOntologyid. VirtualOntologyId is not version-specific.'),
      '#default_value' => variable_get('NCBO_PARAM_ISVIRTUALONTOID', 'true'),
      '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
    );

    $form['ontotoexpand'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Ontologies to expand (Note: If no ontologies are selected, all ontologies will be considered for expansion)'),
      '#description' => t('Specifies the list of ontologies to use with the mapping semantic expansion component.'),
      '#default_value' => variable_get('NCBO_PARAM_ONTOLOGIESTOEXPAND_ARRAY', array()),
      '#options' => $options,
    );

    $form['ontotokeep'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Ontologies to keep in the result (Note: If no ontologies are selected, all ontologies will be kept in the result)'),
      '#description' => t('Specifies the list of ontologies to keep in the result of the annotation process.'),
      '#default_value' => variable_get('NCBO_PARAM_ONTOLOGIESTOKEEP', array()),
      '#options' => $options,
    );

    $form['save'] = array(
      '#type' => 'submit',
      '#submit' =>array('ncboannotator_onto_form_save_submit'),
      '#value' => t('Save ontology settings'),
    );
  }

  $form['refresh'] = array(
    '#type' => 'submit',
    '#submit' =>array('ncboannotator_onto_form_refresh_submit'),
    '#value' => t('Refresh ontology list'),
  );

  return $form;
}

function ncboannotator_onto_form_save_submit($form, &$form_state) {
  if (!array_key_exists('ontotoexpand', $form_state['input'])) {
    variable_set('NCBO_PARAM_ONTOLOGIESTOEXPAND_ARRAY', array());
  } else {
    variable_set('NCBO_PARAM_ONTOLOGIESTOEXPAND_ARRAY', $form_state['input']['ontotoexpand']);
  }

  if (!array_key_exists('ontotokeep', $form_state['input'])) {
    variable_set('NCBO_PARAM_ONTOLOGIESTOKEEP', array());
  } else {
    variable_set('NCBO_PARAM_ONTOLOGIESTOKEEP', $form_state['input']['ontotokeep']);
  }

  variable_set('NCBO_PARAM_ISVIRTUALONTOID', $form_state['input']['isvirtualontoid']);
}

function ncboannotator_onto_form_refresh_submit($form, &$form_state) {
  $annotator = new NCBOAnnotator();
  $onto_list_res = $annotator->retrieve_ontology_list();
  
  $code = $onto_list_res->code;
  $data = $onto_list_res->data;

  $xmlObj = simplexml_load_string($data);
  $arrObjData = get_object_vars($xmlObj);
  
  if ($code != 200) {
    drupal_set_message('Unable to retrieve the ontologies list. Please make sure the connection settings are correct.' , 'error');
    drupal_set_message($arrObjData['shortMessage'] . ': ' . $arrObjData['longMessage'] , 'error');
  } else {
    $object_array = get_object_vars($xmlObj->data->list);
    $count = 0;
    foreach ($object_array['ontologyBean'] as $onto_object) {
      $onto_object_array = get_object_vars($onto_object);
      
      $ontology = array(
	__ONTO_ID__		=> $onto_object_array[__ONTO_ID__],
	__LOCALONTO_ID__		=> $onto_object_array[__LOCALONTO_ID__],
	__ONTO_NAME__		=> $onto_object_array[__ONTO_NAME__],
	__ONTO_STATUS__		=> $onto_object_array[__ONTO_STATUS__],
	__VIRTUALONTO_ID__	=> $onto_object_array[__VIRTUALONTO_ID__],
      );

      $options[$onto_object_array[__ONTO_ID__]] = $onto_object_array[__ONTO_NAME__];
      $ontologies[$count++] = $ontology;
    }
    variable_set('NCBO_CACHED_ONTOLOGIES', $ontologies);
  }
}
