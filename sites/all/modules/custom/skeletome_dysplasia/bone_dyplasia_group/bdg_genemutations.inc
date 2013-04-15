<?php

function bdg_gene_mutations_callback($node) {
  $form_state['build_info']['args'] = array($node);
  return drupal_build_form('bdg_gene_mutations_form', $form_state);
}

function bdg_gene_mutations_form($node, &$form_state) {
  $node = $form_state['build_info']['args'][0];
  $view = views_embed_view('bdg_gene_mutations', 'bdg_gm_block', $node->nid);

  $form['gene_mutations'] = array(
    '#markup' => '<div class="block-title"><h2 class="title">Gene Mutations</h2></div>' . $view,
    '#weight' => 1
  );

  return $form;
}