<?php

function gene_gene_mutations_callback($node) {
  $form_state['build_info']['args'] = array($node);
  return drupal_build_form('gene_gene_mutations_form', $form_state);
}

function gene_gene_mutations_form($node, &$form_state) {
  $node = $form_state['build_info']['args'][0];
  $view = views_embed_view('gene_gene_mutations', 'gene_gm_block', $node->nid);

  $form['gene_mutations'] = array(
    '#markup' => '<div class="block-title"><h2 class="title">Gene Mutations</h2></div>' . $view,
    '#weight' => 1
  );

  return $form;
}