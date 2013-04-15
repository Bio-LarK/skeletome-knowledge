<?php

function bd_xrays_callback($node) {
  $form_state['build_info']['args'] = array($node);
  return drupal_build_form('bd_xrays_form', $form_state);
}

function bd_xrays_form($node, &$form_state) {
  $node = $form_state['build_info']['args'][0];

  $xray_collection_node = node_load($node->field_bd_xrays['und'][0]['target_id']);
  $xray_body = drupal_render(node_view($xray_collection_node, 'media_gallery_block'));

  $form['xray_title'] = array(
    '#markup' => '<div class="block-title"><h2 class="title">X-Ray Collection</h2></div>',
    '#weight' => 1
  );

  $form['xray_body'] = array(
    '#markup' => $xray_body,
    '#weight' => 2
  );

  $form['xray_hr'] = array(
    '#markup' => '<hr style="border:dashed #CCCCCC; border-width:1px 0 0; height:0;" />',
    '#weight' => 3
  );

  return $form;
}