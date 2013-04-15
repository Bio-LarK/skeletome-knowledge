<?php

function _skeletome_statistics_menu() {
  return drupal_get_form('_skeletome_statistics_form');
}

function _skeletome_statistics_form() {
  $form['statistics-bd-head'] = array(
    '#markup' => '<h2 class="title">Bone Dysplasia information</h2>',
    '#weight' => 1
  );

  $form['statistics-bdg'] = array(
    '#markup' => '<h3>Bone dysplasia groups: ' . count_nodes('bone_dysplasia_group') . '</h3>',
    '#weight' => 2
  );

  $form['statistics-bd'] = array(
    '#markup' => '<h3>Bone dysplasias: ' . count_nodes('bone_dysplasia') . '</h3>',
    '#weight' => 3
  );

  $form['statistics-genes'] = array(
    '#markup' => '<h3>Genes: ' . count_nodes('gene') . '</h3>',
    '#weight' => 4
  );

  $form['statistics-genemutations'] = array(
    '#markup' => '<h3>Gene mutation instances: ' . count_nodes('gene_mutation') . '</h3>',
    '#weight' => 5
  );

  $form['statistics-genemutationtypes'] = array(
    '#markup' => '<h3>Gene mutation types: ' . count_nodes('gene_mutation_type') . '</h3>',
    '#weight' => 6
  );

  $form['statistics-vocab-head'] = array(
    '#markup' => '<h2 class="title">Vocabulary information</h2>',
    '#weight' => 7
  );

  $form['statistics-vocab-skeletome'] = array(
    '#markup' => '<h3>Total tags in the SKELETOME vocabulary: ' . count_tags('skeletome_vocabulary') . '</h3>',
    '#weight' => 8
  );

  $form['statistics-vocab-moi'] = array(
    '#markup' => '<h3>Total tags in the Mode of Inheritance vocabulary: ' . count_tags('mode_of_inheritance') . '</h3>',
    '#weight' => 9
  );

  $form['statistics-skeletome-head'] = array(
    '#markup' => '<h2 class="title">SKELETOME general information</h2>',
    '#weight' => 10
  );

  $form['statistics-users'] = array(
    '#markup' => '<h3>Total number of users: ' . count_users() . '</h3>',
    '#weight' => 14
  );

  return $form;
}
