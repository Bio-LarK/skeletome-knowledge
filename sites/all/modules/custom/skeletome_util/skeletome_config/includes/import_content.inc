<?php

function import_content($ontology_file, $content_type) {
  $ontology_url = file_create_url($ontology_file);
  include_once DRUPAL_ROOT . '/sites/all/libraries/arc/ARC2.php';
  $parser = ARC2::getRDFXMLParser();
  $parser->parse($ontology_url);
  $indexed_triples = $parser->getTriples();

  if (ini_get('max_execution_time') > 0) {
      set_time_limit(0);
  }

  if ($content_type === '0') {
    include_once "gmt_import.inc";
    import_gmt($indexed_triples);
  }
  if ($content_type === '1') {
    include_once "gene_import.inc";
    import_genes($indexed_triples);
  }
  if ($content_type === '2') {
    include_once "bd_import.inc";
    import_bds($indexed_triples);
  }
}