<?php

function ncboannotator_params() {
  return drupal_get_form('ncboannotator_params_form');
}

function ncboannotator_params_form() {
  $form['longestmatch'] = array(
    '#type' => 'select',
    '#title' => t('Longest match only'),
    '#description' => t('Specifies either or not the concept recognition step must match the longest words only if they are several concepts that match to an expression.'),
    '#default_value' => variable_get('NCBO_PARAM_LONGESTONLY', 'false'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['wholewordonly'] = array(
    '#type' => 'select',
    '#title' => t('Whole word only'),
    '#description' => t('Specifies whether the concept recognition step must match whole words only or not, if they are several concepts that match to a given word.'),
    '#default_value' => variable_get('NCBO_PARAM_WHOLEWORDONLY', 'true'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['filternumber'] = array(
    '#type' => 'select',
    '#title' => t('Filter number'),
    '#description' => t('Specifies whether the concept recognition step to filter numbers or not.'),
    '#default_value' => variable_get('NCBO_PARAM_FILTERNUMBER', 'true'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['stopwords'] = array(
    '#type' => 'textfield',
    '#title' => t('Stop words list'),
    '#description' => t('Specifies the list of words to exclude from matching (comma separated).'),
    '#default_value' => variable_get('NCBO_PARAM_STOPWORDS', ''),
    '#size' => 150,
  );
  $form['defaultstopwords'] = array(
    '#type' => 'select',
    '#title' => t('With default stop words'),
    '#description' => t('Specifies whether to use the default stop words or not. If this parameter is set to true, this will override any stopWords provided by the user in the "Stop words list" parameter.'),
    '#default_value' => variable_get('NCBO_PARAM_DEFAULTSTOPWORDS', 'true'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['istopwordssensitive'] = array(
    '#type' => 'select',
    '#title' => t('Is stop words case sensitive'),
    '#description' => t('Specifies whether stopwords are case-sensitive or not.'),
    '#default_value' => variable_get('NCBO_PARAM_ISTOPWORDSCASESENSITIVE', 'false'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['mintermsize'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum term length'),
    '#description' => t('Specifies the minimum length of the term to be included in the annotations.'),
    '#default_value' => variable_get('NCBO_PARAM_MINTERMSIZE', '3'),
    '#size' => 2,
    '#maxlength' => 2,
  );
  $form['scored'] = array(
    '#type' => 'select',
    '#title' => t('Scored'),
    '#description' => t('Specifies either or not the annotations are scored. A score is a number assigned to an annotation that reflects the accuracy of the annotation. The higher the score is the better the annotation is. The scoring algorithm gives a specific weight to an annotation according to the context of this annotation. For instance, an annotation done by matching a concept preferred name will be given a higher weight than an annotation done by matching a concept synonym or than an annotation done with a parent level 3 in the is_a hierarchy.'),
    '#default_value' => variable_get('NCBO_PARAM_SCORED', 'true'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['withsynonyms'] = array(
    '#type' => 'select',
    '#title' => t('With synonyms'),
    '#description' => t('Specifies either or not the direct annotations are done with or without Synonyms. By default it includes all the synonyms and preferred name. If "false" is selected, the direct annotations are done with only preferred name.'),
    '#default_value' => variable_get('NCBO_PARAM_WITHSYNONYMS', 'true'),
    '#options' => array('false' => 'FALSE', 'true' => 'TRUE'),
  );
  $form['semantictypes'] = array(
    '#type' => 'textfield',
    '#title' => t('Semantic types'),
    '#description' => t('Specifies the list of semantic types to use in the annotation process. The list of semantic types that can be used is available in the sample HTML page. The values are separated with comma (without spaces). Note that the restriction to semantic types is also applied during the semantic expansion steps.'),
    '#default_value' => variable_get('NCBO_PARAM_SEMANTICTYPES', ''),
    '#size' => 150,
  );
  $form['maxlevel'] = array(
    '#type' => 'textfield',
    '#title' => t('Maximum level'),
    '#description' => t('Specifies the maximum level a parent concept must have to be considered for the is_a semantic closure expansion step. For example, a call done with levelMax=3 will expand a direct annotations done with a concept up to the 3rd level parent in the is_a hierarchy for this concept. A call done with levelMax=0 is equivalent to disable the is_a transitive closure expansion step.'),
    '#default_value' => variable_get('NCBO_PARAM_LEVELMAX', '0'),
    '#size' => 2,
    '#maxlength' => 2,
  );
  $form['mappingtypes'] = array(
    '#type' => 'textfield',
    '#title' => t('Mapping types'),
    '#description' => t('Specifies the list of mapping type to use during the mapping expansion step. The list of semantic types that can be used is available in the sample HTML page. The values are separated with comma (without spaces). The current list contains: NULL, INTER-CUI, FROM-MREL, AUTOMATIC, MANUAL, HUMAN.'),
    '#default_value' => variable_get('NCBO_PARAM_MAPPINGTYPES', 'NULL'),
  );
  $form['format'] = array(
    '#type' => 'select',
    '#title' => t('Format'),
    '#description' => t('Specifies the desired format of the response from Annotator.'),
    '#default_value' => variable_get('NCBO_PARAM_FORMAT', 'xml'),
    '#options' => array('xml' => 'XML', 'text' => 'TEXT', 'tabDelimited' => 'TAB DELIMITED'),
  );

  $form['save'] = array(
    '#type' => 'submit',
    '#submit' =>array('ncboannotator_params_form_submit'),
    '#value' => t('Save parameters'),
  );

  return $form;
}

function ncboannotator_params_form_submit($form, &$form_state) {
  variable_set('NCBO_PARAM_LONGESTONLY', $form_state['input']['longestmatch']);
  variable_set('NCBO_PARAM_WHOLEWORDONLY', $form_state['input']['wholewordonly']);
  variable_set('NCBO_PARAM_FILTERNUMBER', $form_state['input']['filternumber']);
  variable_set('NCBO_PARAM_STOPWORDS', $form_state['input']['stopwords']);
  variable_set('NCBO_PARAM_DEFAULTSTOPWORDS', $form_state['input']['defaultstopwords']);
  variable_set('NCBO_PARAM_ISTOPWORDSCASESENSITIVE', $form_state['input']['istopwordssensitive']);
  variable_set('NCBO_PARAM_MINTERMSIZE', $form_state['input']['mintermsize']);
  variable_set('NCBO_PARAM_SCORED', $form_state['input']['scored']);
  variable_set('NCBO_PARAM_WITHSYNONYMS', $form_state['input']['withsynonyms']);
  variable_set('NCBO_PARAM_SEMANTICTYPES', $form_state['input']['semantictypes']);
  variable_set('NCBO_PARAM_LEVELMAX', $form_state['input']['maxlevel']);
  variable_set('NCBO_PARAM_MAPPINGTYPES', $form_state['input']['mappingtypes']);
  variable_set('NCBO_PARAM_FORMAT', $form_state['input']['format']);
}