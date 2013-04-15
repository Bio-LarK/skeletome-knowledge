<?php

function ncboannotator_settings() {
  return drupal_get_form('ncboannotator_settings_form');
}

function ncboannotator_settings_form() {
  $form['host_name'] = array(
    '#type' => 'textfield',
    '#title' => t('NCBO Annotator host name'),
    '#description' => t('Specify the host name of the NCBO Annotator.'),
    '#default_value' => variable_get('NCBO_HOST', 'rest.bioontology.org'),
    '#required' => TRUE,
  );

  $form['host_port'] = array(
    '#type' => 'textfield',
    '#title' => t('NCBO Annotator host port'),
    '#size' => 5,
    '#maxlength' => 5,
    '#description' => t('Specify the host port of the NCBO Annotator. Enter 0 if the default port 80 should be used.'),
    '#default_value' => variable_get('NCBO_PORT', '0'),
    '#required' => TRUE,
  );

  $form['webapp_path'] = array(
    '#type' => 'textfield',
    '#title' => t('NCBO Annotator Web app path'),
    '#description' => t('Specify the Web app path of the NCBO Annotator.'),
    '#default_value' => variable_get('NCBO_PATH','/obs/annotator'),
    '#required' => TRUE,
  );

  $form['apikey'] = array(
    '#type' => 'textfield',
    '#title' => t('NCBO Annotator Web API Key'),
    '#description' => t('Specify the API Key of the NCBO Annotator.'),
    '#default_value' => variable_get('NCBO_APIKEY', '24e68634-54e0-11e0-9d7b-005056aa3316'),
    '#required' => TRUE,
  );

  $form['onto_path'] = array(
    '#type' => 'textfield',
    '#title' => t('NCBO Annotator Ontologies path'),
    '#description' => t('Specify the path to the latest version of the supported Ontologies file.'),
    '#default_value' => variable_get('NCBO_ONTOPATH', '/obs/ontologies'),
    '#required' => TRUE,
  );

  $form['onto_statuscode'] = array(
    '#type' => 'textfield',
    '#title' => t('NCBO Annotator ontologies status code'),
    '#size' => 5,
    '#maxlength' => 5,
    '#description' => t('Specify the status code used to identify the ontologies currently considered by the NCBO Annotator.'),
    '#default_value' => variable_get('NCBO_ONTOSTATUSCODE', '28'),
    '#required' => TRUE,
  );

  $form['save'] = array(
    '#type' => 'submit',
    '#submit' =>array('ncboannotator_settings_form_save_submit'),
    '#value' => t('Save settings'),
  );

  $form['test'] = array(
    '#type' => 'submit',
    '#submit' =>array('ncboannotator_settings_form_test_submit'),
    '#value' => t('Test connection'),
  );

  return $form;
}

function ncboannotator_settings_form_save_submit($form, &$form_state) {
  variable_set('NCBO_HOST', $form_state['input']['host_name']);
  variable_set('NCBO_PORT', $form_state['input']['host_port']);
  variable_set('NCBO_PATH', $form_state['input']['webapp_path']);
  variable_set('NCBO_APIKEY', $form_state['input']['apikey']);
  variable_set('NCBO_ONTOPATH', $form_state['input']['onto_path']);
  variable_set('NCBO_ONTOSTATUSCODE', $form_state['input']['onto_statuscode']);
}

function ncboannotator_settings_form_test_submit($form, &$form_state) {
  variable_set('NCBO_HOST', $form_state['input']['host_name']);
  variable_set('NCBO_PORT', $form_state['input']['host_port']);
  variable_set('NCBO_PATH', $form_state['input']['webapp_path']);
  variable_set('NCBO_APIKEY', $form_state['input']['apikey']);
  variable_set('NCBO_ONTOPATH', $form_state['input']['onto_path']);
  variable_set('NCBO_ONTOSTATUSCODE', $form_state['input']['onto_statuscode']);

  $annotator = new NCBOAnnotator();
  $onto_list_res = $annotator->retrieve_ontology_list();
  
  $code = $onto_list_res->code;
  $data = $onto_list_res->data;

  $xmlObj = simplexml_load_string($data);
  $arrObjData = get_object_vars($xmlObj);
  $onto_up = FALSE;
  
  if ($code == 200) {
    drupal_set_message('Ontologies retrieval test successful!');
    $onto_up = TRUE;
  } else {
    drupal_set_message($arrObjData['shortMessage'] . ': ' . $arrObjData['longMessage'] , 'error');
    variable_set('NCBO_ACTIVE', FALSE);
  }

  $annot_test = $annotator->test_call();

  $code = $annot_test->code;
  $data = $annot_test->data;

  $xmlObj = simplexml_load_string($data);
  $arrObjData = get_object_vars($xmlObj);

  if ($code == 200) {
    drupal_set_message('Annotation test successful!');
    if ($onto_up) {
      variable_set('NCBO_ACTIVE', TRUE);
    }
  } else {
    variable_set('NCBO_ACTIVE', FALSE);
    drupal_set_message($arrObjData['shortMessage'] . ': ' . $arrObjData['longMessage'] , 'error');
  }
}
