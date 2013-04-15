<?php

define('__GENE_CLASS__', 'http://purl.org/skeletome/bonedysplasia#Gene');
define('__GENE_MUTATION_CLASS__', 'http://purl.org/skeletome/bonedysplasia#Gene_Mutation');

define('__RDFS_SUBCLASSOF__', 'http://www.w3.org/2000/01/rdf-schema#subClassOf');
define('__RDFS_LABEL__', 'http://www.w3.org/2000/01/rdf-schema#label');
define('__SKOS_ALTLABEL__', 'http://www.w3.org/2004/02/skos/core#altLabel');
define('__OWL_ALLVALUESFROM__', 'http://www.w3.org/2002/07/owl#allValuesFrom');
define('__OWL_SOMEVALUESFROM__', 'http://www.w3.org/2002/07/owl#someValuesFrom');
define('__OWL_ONPROPERTY__', 'http://www.w3.org/2002/07/owl#onProperty');

define('__BD_LOCUS__', 'http://purl.org/skeletome/bonedysplasia#chromosomal_locus');
define('__BD_OMIM__', 'http://purl.org/skeletome/bonedysplasia#omim_no');
define('__BD_GO_TERM__', 'http://purl.org/skeletome/bonedysplasia#go_term');
define('__BD_MESH_ID__', 'http://purl.org/skeletome/bonedysplasia#mesh_id');
define('__BD_UNIPROT_ID__', 'http://purl.org/skeletome/bonedysplasia#uniprot_id');
define('__BD_ENTREZGENE_ID__', 'http://purl.org/skeletome/bonedysplasia#entrezgene_id');
define('__BD_UMLS_CUI__', 'http://purl.org/skeletome/bonedysplasia#umls_cui');
define('__BD_REFSEQ__', 'http://purl.org/skeletome/bonedysplasia#ref_seq');
define('__BD_ACCESSION_NO__', 'http://purl.org/skeletome/bonedysplasia#accession_no');

define('__BD_ENCODING__', 'http://purl.org/skeletome/bonedysplasia#encoding');
define('__BD_OFFSET__', 'http://purl.org/skeletome/bonedysplasia#offset');
define('__BD_COUNT__', 'http://purl.org/skeletome/bonedysplasia#count');
define('__BD_ORIGINALCONTENT__', 'http://purl.org/skeletome/bonedysplasia#original_content');
define('__BD_MUTATEDCONTENT__', 'http://purl.org/skeletome/bonedysplasia#mutated_content');

define('__BD_INVOLVES__', 'http://purl.org/skeletome/bonedysplasia#has_locus');
define('__BD_MUTATION_TYPE__', 'http://purl.org/skeletome/bonedysplasia#mutation_type');

define('__GENE_CONTENTTYPE__', 'gene');
define('__GENE_MUTATION_CONTENTTYPE__', 'gene_mutation');

function import_genes($indexed_triples) {
  $data_spo = array();
  foreach ($indexed_triples as $indx) {
    if (array_key_exists($indx['s'], $data_spo)) {
      $pred = $data_spo[$indx['s']];
      if (array_key_exists($indx['p'], $pred)) {
	$obj = $pred[$indx['p']];
	if (!array_key_exists($indx['o'], $obj)) {
	  array_push($obj, $indx['o']);
	}
	$pred[$indx['p']] = $obj;
      } else {
	$obj = array();
	$obj[] = $indx['o'];
	$pred[$indx['p']] = $obj;
      }
      $data_spo[$indx['s']] = $pred;
    } else {
      $pred = array();
      $obj = array();
      $obj[] = $indx['o'];
      $pred[$indx['p']] = $obj;
      $data_spo[$indx['s']] = $pred;
    }
  }

  $gene_uris = array();
  $gene_mutation_uris = array();

  foreach ($indexed_triples as $indx) {
    if ($indx['p'] == __RDFS_SUBCLASSOF__) {
      if ($indx['o'] == __GENE_CLASS__) {
	$gene_uris[] = $indx['s'];
      }
      if ($indx['o'] == __GENE_MUTATION_CLASS__) {
	$gene_mutation_uris[] = $indx['s'];
      }
    }    
  }

  $gene_nids = _create_genes($data_spo, $gene_uris);
  $gene_mutation_nids = _create_gene_mutations($indexed_triples, $gene_nids, $data_spo, $gene_mutation_uris);
}

function _create_genes($data_spo, $gene_uris) {
  $gene_nids = array();
  foreach ($gene_uris as $gene_uri) {
    $gene_nid = _create_gene($gene_uri, $data_spo);
    $gene_nids[$gene_uri] = $gene_nid;
  }

  drupal_set_message('Total number of Genes imported: ' . count($gene_nids));
  return $gene_nids;
}

function _create_gene($gene_uri, $data_spo) {
  $gene_node = new stdClass();
  $gene_node->type = __GENE_CONTENTTYPE__;
  $gene_node->language = 'und'; 
  $gene_node->uid = 1;
  $gene_node->field_gene_uri[LANGUAGE_NONE][0]['value'] = $gene_uri;

  $predicates = $data_spo[$gene_uri];
  if ($predicates[__RDFS_LABEL__][0] == "Unknown") {
    $gene_node->title = "Unknown gene";
  } else {
    $gene_node->title = $predicates[__RDFS_LABEL__][0];
  }

  if (array_key_exists(__SKOS_ALTLABEL__, $predicates)) {
    $objs = $predicates[__SKOS_ALTLABEL__];
    $altLabel_count = 0;
    foreach ($objs as $obj) {
      $gene_node->field_gene_synonym[LANGUAGE_NONE][$altLabel_count++]['value'] = $obj;
    }
  }
  if (array_key_exists(__BD_LOCUS__, $predicates)) {
    $gene_node->field_gene_locus[LANGUAGE_NONE][0]['value'] = $predicates[__BD_LOCUS__][0];
  }
  if (array_key_exists(__BD_OMIM__, $predicates)) {
    $gene_node->field_gene_omim[LANGUAGE_NONE][0]['value'] = $predicates[__BD_OMIM__][0];
  }  
  if (array_key_exists(__BD_UNIPROT_ID__, $predicates)) {
    $gene_node->field_gene_uniprot[LANGUAGE_NONE][0]['value'] = $predicates[__BD_UNIPROT_ID__][0];
  }  
  if (array_key_exists(__BD_MESH_ID__, $predicates)) {
    $gene_node->field_gene_mesh[LANGUAGE_NONE][0]['value'] = $predicates[__BD_MESH_ID__][0];
  }  
  if (array_key_exists(__BD_GO_TERM__, $predicates)) {
    $gene_node->field_gene_go[LANGUAGE_NONE][0]['value'] = $predicates[__BD_GO_TERM__][0];
  }  
  if (array_key_exists(__BD_ENTREZGENE_ID__, $predicates)) {
    $gene_node->field_gene_entrezgene[LANGUAGE_NONE][0]['value'] = $predicates[__BD_ENTREZGENE_ID__][0];
  }  
  if (array_key_exists(__BD_UMLS_CUI__, $predicates)) {
    $gene_node->field_gene_umlscui[LANGUAGE_NONE][0]['value'] = $predicates[__BD_UMLS_CUI__][0];
  }  
  if (array_key_exists(__BD_REFSEQ__, $predicates)) {
    $gene_node->field_gene_refseq[LANGUAGE_NONE][0]['value'] = $predicates[__BD_REFSEQ__][0];
  }  
  if (array_key_exists(__BD_ACCESSION_NO__, $predicates)) {
    $gene_node->field_gene_accession[LANGUAGE_NONE][0]['value'] = $predicates[__BD_ACCESSION_NO__][0];
  }  

  node_save($gene_node);
  return $gene_node->nid;
}


function _create_gene_mutations($indexed_triples, $gene_nids, $data_spo, $gene_mutation_uris) {
  $gene_mutation_nids = array();
  $gm_types = _find_gm_types();

  foreach ($gene_mutation_uris as $gene_mutation_uri) {
    $gene_mutation_nid = _create_gene_mutation($gene_mutation_uri, $indexed_triples, $gene_nids, $data_spo, $gm_types);
    $gene_mutation_nids[$gene_mutation_uri] = $gene_mutation_nid;
  }

  drupal_set_message('Total number of Gene Mutations imported: ' . count($gene_mutation_nids));

  return $gene_mutation_nids;
}

function _create_gene_mutation($gene_mutation_uri, $indexed_triples, $gene_nids, $data_spo, $gm_types) {
  $gene_mutation_node = new stdClass();
  $gene_mutation_node->type = __GENE_MUTATION_CONTENTTYPE__;
  $gene_mutation_node->language = 'und'; 
  $gene_mutation_node->uid = 1;
  $gene_mutation_node->field_gene_mutation_uri[LANGUAGE_NONE][0]['value'] = $gene_mutation_uri;

  $gene_uri = NULL;
  $gene_node = NULL;
  $classes = $data_spo[$gene_mutation_uri][__RDFS_SUBCLASSOF__];
  foreach ($classes as $class) {
    if ($class != __GENE_MUTATION_CLASS__) {
      $gene_uri = _find_gene_uri($indexed_triples, $data_spo, $class);
      if ($gene_uri != NULL) {
	break;
      }
    }
  }

  $predicates = $data_spo[$gene_mutation_uri];
  if ($gene_uri != NULL) {
    $gene_nid = $gene_nids[$gene_uri];
    $gene_mutation_node->field_gene_mutation_gene[LANGUAGE_NONE][0]['target_id'] = $gene_nid;
    $gene_node = node_load($gene_nid);    

    if (array_key_exists(__BD_ENCODING__, $predicates)) {
      $gene_mutation_node->title = $predicates[__BD_ENCODING__][0];
    } else {
      $gene_mutation_node->title = $gene_node->title . ' (unspecified mutation)';
    }
  }

  if (array_key_exists(__BD_MUTATEDCONTENT__, $predicates)) {
    $gene_mutation_node->field_gm_mutatedcontent[LANGUAGE_NONE][0]['value'] = $predicates[__BD_MUTATEDCONTENT__][0];
  }  
  if (array_key_exists(__BD_OFFSET__, $predicates)) {
    $gene_mutation_node->field_gm_offset[LANGUAGE_NONE][0]['value'] = $predicates[__BD_OFFSET__][0];
  }  
  if (array_key_exists(__BD_ORIGINALCONTENT__, $predicates)) {
    $gene_mutation_node->field_gm_originalcontent[LANGUAGE_NONE][0]['value'] = $predicates[__BD_ORIGINALCONTENT__][0];
  }  
  if (array_key_exists(__BD_COUNT__, $predicates)) {
    $gene_mutation_node->field_gm_count[LANGUAGE_NONE][0]['value'] = $predicates[__BD_COUNT__][0];
  }  

  $gmtype_uri = NULL;
  foreach ($classes as $class) {
    if ($class != __GENE_MUTATION_CLASS__) {
      $gmtype_uri = _find_genemutation_uri($indexed_triples, $data_spo, $class);
      if ($gmtype_uri != NULL) {
	break;
      }
    }
  }

  node_save($gene_mutation_node);

  if ($gmtype_uri != NULL) {
    $gene_mutation_node->field_gm_mutation_type[LANGUAGE_NONE][0]['target_id'] = $gm_types[$gmtype_uri];
    
    $gmt_node = node_load($gm_types[$gmtype_uri]);
    $count = 0;
    if (array_key_exists('und', $gmt_node->field_gm_type_gene_mutation)) {
      $count = count($gmt_node->field_gm_type_gene_mutation[LANGUAGE_NONE]);
    }
    $gmt_node->field_gm_type_gene_mutation[LANGUAGE_NONE][$count]['target_id'] = $gene_mutation_node->nid;

    node_save($gmt_node);
  }

  node_save($gene_mutation_node);
  
  if ($gene_node != NULL) {
    if (empty($gene_node->field_gene_gene_mutation)) {
      $gene_node->field_gene_gene_mutation[LANGUAGE_NONE][0]['target_id'] = $gene_mutation_node->nid;
    } else {
      $length = count($gene_node->field_gene_gene_mutation[LANGUAGE_NONE]);
      $gene_node->field_gene_gene_mutation[LANGUAGE_NONE][$length]['target_id'] = $gene_mutation_node->nid;
    }
    node_save($gene_node);
  }

  return $gene_mutation_node->nid;
}

function _find_gene_uri($indexed_triples, $data_spo, $bnode) {
  $predicates = $data_spo[$bnode];
  $onProperty = $predicates[__OWL_ONPROPERTY__][0];
  if ($onProperty == __BD_INVOLVES__) {
    if (array_key_exists(__OWL_ALLVALUESFROM__, $predicates)) {
      return $predicates[__OWL_ALLVALUESFROM__][0];
    } else {
      if (array_key_exists(__OWL_SOMEVALUESFROM__, $predicates)) {
	return $predicates[__OWL_SOMEVALUESFROM__][0];
      }
    }
  }

  return NULL;
}

function _find_genemutation_uri($indexed_triples, $data_spo, $bnode) {
  $predicates = $data_spo[$bnode];
  $onProperty = $predicates[__OWL_ONPROPERTY__][0];
  if ($onProperty == __BD_MUTATION_TYPE__) {
    if (array_key_exists(__OWL_ALLVALUESFROM__, $predicates)) {
      return $predicates[__OWL_ALLVALUESFROM__][0];
    } else {
      if (array_key_exists(__OWL_SOMEVALUESFROM__, $predicates)) {
	return $predicates[__OWL_SOMEVALUESFROM__][0];
      }
    }
  }

  return NULL;
}

function _find_gm_types() {
  $gm_types = array();

  $sql = 'SELECT nid FROM {node} n WHERE n.type = :type';
  $result = db_query($sql, array(':type' => 'gene_mutation_type'));
  foreach ($result as $row) {
    $gmt_node = node_load($row->nid);
    $gm_types[$gmt_node->field_gene_mutation_type_uri[LANGUAGE_NONE][0]['value']] = $row->nid;
  }

  return $gm_types;
}