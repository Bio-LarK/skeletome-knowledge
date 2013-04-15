<?php

define('__RDF_TYPE__', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
define('__OWL_CLASS__', 'http://www.w3.org/2002/07/owl#Class');
define('__OWL_THING__', 'http://www.w3.org/2002/07/owl#Thing');
define('__RDFS_SUBCLASSOF__', 'http://www.w3.org/2000/01/rdf-schema#subClassOf');
define('__RDFS_LABEL__', 'http://www.w3.org/2000/01/rdf-schema#label');
define('__UMLS_CUI__', 'http://purl.org/skeletome/bonedysplasia#umls_cui');
define('__CLASS_MUTATIONABNORMALITY__', 'http://ncicb.nci.nih.gov/xml/owl/EVS/Thesaurus.owl#Mutation_Abnormality');

define('__GENE_MUTATION_TYPE_CONTENTTYPE__', 'gene_mutation_type');

function import_gmt($indexed_triples) {
  $count = 0;
  $gmts = array();
  foreach ($indexed_triples as $indx) {
    if ($indx['p'] == __RDF_TYPE__ && $indx['o'] == __OWL_CLASS__ && $indx['s'] != __OWL_THING__) {
      $count++;
      $nid = _create_genemutationtype($indexed_triples, $indx['s']);
      $gmts[$indx['s']] = $nid;
    }
  }

  foreach ($gmts as $gmt_uri => $gmt_nid) {
    _find_children($indexed_triples, $gmt_uri, $gmt_nid, $gmts);
  }

  drupal_set_message('Total number of Gene Mutation types imported: ' . $count);
}

function _create_genemutationtype($indexed_triples, $gmt_uri) {
  $mgt_node = new stdClass();
  $mgt_node->type = __GENE_MUTATION_TYPE_CONTENTTYPE__;
  $mgt_node->language = 'und'; 
  $mgt_node->uid = 1;
  $mgt_node->field_gene_mutation_type_uri[LANGUAGE_NONE][0]['value'] = $gmt_uri;

  $cui_count = 0;
  foreach ($indexed_triples as $indx) {
    if ($indx['s'] == $gmt_uri) {
      if ($indx['p'] == __RDFS_LABEL__) {
	$mgt_node->title = $indx['o'];
      }
      if ($indx['p'] == __UMLS_CUI__) {
	$mgt_node->field_gene_mutation_type_umls[LANGUAGE_NONE][$cui_count++]['value'] = $indx['o'];
      }
    }
  }
  node_save($mgt_node);
  return $mgt_node->nid;
}

function _find_children($indexed_triples, $gmt_uri, $gmt_nid, $gmts) {
  $children = array();
  foreach ($indexed_triples as $indx) {
    if ($indx['p'] == __RDFS_SUBCLASSOF__ && $indx['o'] == $gmt_uri) {
      $children[] = $indx['s'];
    }
  }

  if (!empty($children)) {
    $gmt_node = node_load($gmt_nid);
    
    $count = 0;
    foreach ($children as $child_uri) {
      $child_nid = $gmts[$child_uri];
      $gmt_node->field_gm_type_subtype[LANGUAGE_NONE][$count++]['target_id'] = $child_nid;
      
      $child_node = node_load($child_nid);
      $child_node->field_gm_type_supertype[LANGUAGE_NONE][0]['target_id'] = $gmt_nid;
      node_save($child_node);
    }

    node_save($gmt_node);
  }
}