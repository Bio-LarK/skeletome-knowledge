<?php

function get_phenotype_callback() {
  $max_count = $_GET['max_count'];
  $term_startsWith = $_GET['term_startsWith'];

  debug($term_startsWith);

  $vid = get_vid_for_vocabulary('skeletome_vocabulary');
  $terms = get_terms_for_approx_name_with_limit($vid, $term_startsWith, $max_count);
  $s = create_pheno_result($terms);

  debug($s);

  echo $s;
}

function create_pheno_result($terms) {
  $result = '{"totalResultsCount":' . count($terms) . ',"terms":[';

  $count = 0;
  foreach ($terms as $tid => $term) {
    $term_name = $term;
    if (strlen($term_name) > 55) {
      $term_name = substr($term_name, 0, 55) . ' ...';
    }
    $term_s = '{"term":"' . $term_name . '","tid":"' . $tid . '"},';
    $result = $result . $term_s;
    $count++;
  }
  if ($count != 0) {
    $result = substr($result, 0, strlen($result) - 1);
  }

  $result = $result . ']}';
  return $result;
}

function get_terms_for_approx_name($vid, $name) {
  $terms = array();

  $sql = 'SELECT tid, vid, name FROM {taxonomy_term_data} WHERE vid = :vid AND name LIKE :name';
  $result = db_query($sql, array(':vid' => $vid, ':name' => '%' . $name . '%'));
  foreach ($result as $r) {
    $terms[$r->tid] = $r->name;
  }

  return $terms;
}

function get_terms_for_approx_name_with_limit($vid, $name, $limit) {
  $terms = array();

  $sql = 'SELECT tid, vid, name FROM {taxonomy_term_data} WHERE vid = :vid AND name LIKE :name LIMIT 0, ' . $limit;
  $result = db_query($sql, array(':vid' => $vid, ':name' => '%' . $name . '%'));
  foreach ($result as $r) {
    $terms[$r->tid] = $r->name;
  }

  return $terms;
}

function get_biblio_callback() {
  if (array_key_exists('pubmedID', $_GET)) {
    $pubmedID = $_GET['pubmedID'];
    $nid = get_biblio_for_pubmedid($pubmedID);
    if ($nid === NULL) {
      $Eclient = new BiblioEntrezClient;
      try {
	$result = $Eclient->fetch($pubmedID);
      } catch (Exception $e) {
	form_set_error($e->getMessage());
      }

      global $user;
      $data = NULL;
      if (!isset($result->PubmedArticle)) {
	if (isset($result->PubmedBookArticle)) {
	  $data = new BiblioEntrezPubmedBookArticle($result->PubmedBookArticle);
	}
      } else {
	$data = new BiblioEntrezPubmedArticle($result->PubmedArticle);
      }

      if ($data !== NULL) {
	$node_data = $data->getBiblio();
	$node_data['type'] = 'biblio';
	$node_data['uid'] = $user->uid;
	$node = (object) $node_data;
	node_save($node);
	$s = '{"nid":' . $node->nid .'}';
      } else {
	$s = '{"nid":"null"}';
      }
    } else {
      $s = '{"nid":' . $nid . '}';
    }
    echo $s;
  } else {
    $max_count = $_GET['max_count'];
    $biblio_startsWith = $_GET['biblio_startsWith'];
    $biblio = get_biblio_for_approx_title_with_limit($biblio_startsWith, $max_count);
    $s = create_biblio_result($biblio);
    echo $s;
  }
}

function get_biblio_for_pubmedid($pubmedID) {
  $nid = NULL;
  $sql = 'SELECT biblio_pubmed_id, nid FROM {biblio_pubmed} WHERE biblio_pubmed_id = :pubmedid';
  $result = db_query($sql, array(':pubmedid' => $pubmedID));
  foreach ($result as $r) {
    $nid = $r->nid;
  }

  return $nid;
}

function create_biblio_result($biblio) {
  $result = '{"totalResultsCount":' . count($biblio) . ',"pubs":[';

  $count = 0;
  foreach ($biblio as $nid => $biblio_item) {
    $authors = get_authors_for_biblio($nid);
    if ($authors !== '') {
      if (strlen($authors) > 55) {
	$authors = substr($authors, 0, 55) . ' ...';
      }
    }
    $title = $biblio_item;
    if (strlen($title) > 55) {
      $title = substr($title, 0, 55) . ' ...';
    }
    $pub_s = '{"pub":"' . $title . '","nid":"' . $nid . '","auth":"' . $authors . '"},';
    $result = $result . $pub_s;
    $count++;
  }
  if ($count != 0) {
    $result = substr($result, 0, strlen($result) - 1);
  }

  $result = $result . ']}';
  return $result;
}

function get_biblio_for_approx_title_with_limit($title, $limit) {
  $biblio = array();

  $sql = 'SELECT nid, title, type FROM {node} WHERE type = :type AND title LIKE :title LIMIT 0, ' . $limit;
  $result = db_query($sql, array(':type' => 'biblio', ':title' => '%' . $title . '%'));
  foreach ($result as $r) {
    $biblio[$r->nid] = $r->title;
  }

  return $biblio;
}

function get_authors_for_biblio($nid) {
  $authors = array();
  $cids = array();
  $sql = 'SELECT nid, cid, rank FROM {biblio_contributor} WHERE nid = :nid';
  $result = db_query($sql, array(':nid' => $nid));
  foreach ($result as $r) {
    $cids[$r->cid] = $r->rank;
  }
  asort($cids);

  foreach ($cids as $cid => $rank) {
    $sql = 'SELECT cid, lastname, firstname FROM {biblio_contributor_data} WHERE cid = :cid';
    $result = db_query($sql, array(':cid' => $cid));
    foreach ($result as $r) {
      $abbrev_n = abbrev_name($r->lastname, $r->firstname);
      if ($abbrev_n != NULL) {
	$authors[$cid] = $abbrev_n;
      }
    }
  }

  $final_s = '';
  foreach ($authors as $cid => $name) {
    $final_s = $final_s . $name . ', ';
  }
  if ($final_s !== '') {
    $final_s = substr($final_s, 0, strlen($final_s) - 2);
  }

  return $final_s;
}

function abbrev_name($lastname, $firstname) {
  if ($lastname != NULL && $firstname != NULL) {
    if ($lastname !== '' && $firstname !== '') {
      return $lastname . ', ' . substr($firstname, 0, 1) . '.';
    }
  }

  return NULL;
}

function add_scc_to_biblio($pub_nid, $scc_nid) {
  $pub_node = node_load($pub_nid);
  if (array_key_exists('und', $pub_node->field_biblio_ssc_field)) {
    $count = count($pub_node->field_biblio_ssc_field['und']);
    $pub_node->field_biblio_ssc_field['und'][$count]['nid'] = $scc_nid;
  } else {
    $pub_node->field_biblio_ssc_field['und'][0]['nid'] = $scc_nid;
  }
  node_save($pub_node);
}
