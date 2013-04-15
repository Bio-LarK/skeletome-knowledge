<?php
// $Id: NCBOAnnotator.inc $

define('__PARAM_LONGESTONLY__', 'longestOnly');
define('__PARAM_WHOLEWORDONLY__', 'wholeWordOnly');
define('__PARAM_FILTERNUMBER__', 'filterNumber');
define('__PARAM_STOPWORDS__', 'stopWords');
define('__PARAM_DEFAULTSTOPWORDS__', 'withDefaultStopWords');
define('__PARAM_STOPWORDCS__', 'isTopWordsCaseSensitive');
define('__PARAM_MINTERMSIZE__', 'mintermSize');
define('__PARAM_SCORED__', 'scored');
define('__PARAM_WITHSYNONYMS__', 'withSynonyms');
define('__PARAM_ONTOTOEXPAND__', 'ontologiesToExpand');
define('__PARAM_ONTOTOKEEP__', 'ontologiesToKeepInResult');
define('__PARAM_VIRTUALONTOID__', 'isVirtualOntologyId');
define('__PARAM_SEMANTICTYPES__', 'semanticTypes');
define('__PARAM_LEVELMAX__', 'levelMax');
define('__PARAM_MAPPINGTYPES__', 'mappingTypes');
define('__PARAM_TEXTTOANNOTATE__', 'textToAnnotate');
define('__PARAM_FORMAT__', 'format');
define('__PARAM_APIKEY__', 'apikey');

class NCBOAnnotator {

  private $defaults = array(
    'protocol' => 'http',
  );

  private $headers = array(
    'Content-Type' => 'application/x-www-form-urlencoded',
    'User-Agent' => 'Drupal Annotator Client',
  );

  public $parameters;

  function __construct($options = array()) {
    $this->defaults["host"] = variable_get('NCBO_HOST');
    $this->defaults["port"] = variable_get('NCBO_PORT');
    $this->defaults["path"] = variable_get('NCBO_PATH');
    $this->defaults["apikey"] = variable_get('NCBO_APIKEY');
    $this->defaults["ontopath"] = variable_get('NCBO_ONTOPATH');

    $this->parameters = array_merge($this->defaults, $options);
    $this->parameters["host"] = preg_replace(",^https?://,i", "", $this->parameters["host"]);
  }

  public function retrieve_ontology_list() {
    if ($this->parameters['port'] != 0) {
      $uri = $this->parameters['protocol'] . '://' . $this->parameters['host'] . ':' . $this->parameters['port'] . $this->parameters['ontopath'] . '?' . __PARAM_APIKEY__ . '=' . $this->defaults["apikey"];
    } else {
      $uri = $this->parameters['protocol'] . '://' . $this->parameters['host'] . $this->parameters['ontopath'] . '?' . __PARAM_APIKEY__ . '=' . $this->defaults["apikey"];
    }

    $ret = drupal_http_request($uri, array());
    return $ret;
  }

  public function annotate($text) {
    if ($this->parameters['port'] != 0) {
      $uri = $this->parameters['protocol'] . '://' . $this->parameters['host'] . ':' . $this->parameters['port'] . $this->defaults["path"];
    } else {
      $uri = $this->parameters['protocol'] . '://' . $this->parameters['host'] . $this->defaults["path"];
    }

    $onto_array = variable_get('NCBO_PARAM_ONTOLOGIESTOEXPAND_ARRAY');
    $ontoToExpand = $this->getOntologies($onto_array);

    $onto_array = variable_get('NCBO_PARAM_ONTOLOGIESTOKEEP');
    $ontoToKeep = $this->getOntologies($onto_array);

    $options = array(
      __PARAM_LONGESTONLY__ 		=> variable_get('NCBO_PARAM_LONGESTONLY', 'false'),
      __PARAM_WHOLEWORDONLY__		=> variable_get('NCBO_PARAM_WHOLEWORDONLY', 'true'),
      __PARAM_FILTERNUMBER__		=> variable_get('NCBO_PARAM_FILTERNUMBER', 'true'),
      __PARAM_STOPWORDS__		=> variable_get('NCBO_PARAM_STOPWORDS', ''),
      __PARAM_DEFAULTSTOPWORDS__	=> variable_get('NCBO_PARAM_DEFAULTSTOPWORDS', 'true'),
      __PARAM_STOPWORDCS__		=> variable_get('NCBO_PARAM_ISTOPWORDSCASESENSITIVE', 'false'),
      __PARAM_MINTERMSIZE__		=> variable_get('NCBO_PARAM_MINTERMSIZE', '3'),
      __PARAM_SCORED__			=> variable_get('NCBO_PARAM_SCORED', 'true'),
      __PARAM_WITHSYNONYMS__		=> variable_get('NCBO_PARAM_WITHSYNONYMS', 'true'),
      __PARAM_VIRTUALONTOID__		=> variable_get('NCBO_PARAM_ISVIRTUALONTOID', 'true'),
      __PARAM_SEMANTICTYPES__		=> variable_get('NCBO_PARAM_SEMANTICTYPES', ''),
      __PARAM_LEVELMAX__		=> variable_get('NCBO_PARAM_LEVELMAX', '0'),
      __PARAM_MAPPINGTYPES__		=> variable_get('NCBO_PARAM_MAPPINGTYPES', ''),
      __PARAM_FORMAT__			=> variable_get('NCBO_PARAM_FORMAT', 'xml'),
      __PARAM_APIKEY__			=> variable_get('NCBO_APIKEY', ''),
      __PARAM_ONTOTOEXPAND__		=> $ontoToExpand,
      __PARAM_ONTOTOKEEP__		=> $ontoToKeep,
      __PARAM_TEXTTOANNOTATE__		=> $text,
    );

    $data_enc = http_build_query($options, '', '&');
    $params = array(
      'method' 	=> 'POST',
      'data'	=> $data_enc
    );

    $ret = drupal_http_request($uri, $params);
    return $ret;
  }

  public function test_call() {
    if ($this->parameters['port'] != 0) {
      $uri = $this->parameters['protocol'] . '://' . $this->parameters['host'] . ':' . $this->parameters['port'] . $this->defaults["path"];
    } else {
      $uri = $this->parameters['protocol'] . '://' . $this->parameters['host'] . $this->defaults["path"];
    }

    $options = array(
      __PARAM_LONGESTONLY__ 		=> variable_get('NCBO_PARAM_LONGESTONLY', 'false'),
      __PARAM_WHOLEWORDONLY__		=> variable_get('NCBO_PARAM_WHOLEWORDONLY', 'true'),
      __PARAM_FILTERNUMBER__		=> variable_get('NCBO_PARAM_FILTERNUMBER', 'true'),
      __PARAM_STOPWORDS__		=> variable_get('NCBO_PARAM_STOPWORDS', ''),
      __PARAM_DEFAULTSTOPWORDS__	=> variable_get('NCBO_PARAM_DEFAULTSTOPWORDS', 'true'),
      __PARAM_STOPWORDCS__		=> variable_get('NCBO_PARAM_ISTOPWORDSCASESENSITIVE', 'false'),
      __PARAM_MINTERMSIZE__		=> variable_get('NCBO_PARAM_MINTERMSIZE', '3'),
      __PARAM_SCORED__			=> variable_get('NCBO_PARAM_SCORED', 'true'),
      __PARAM_WITHSYNONYMS__		=> variable_get('NCBO_PARAM_WITHSYNONYMS', 'true'),
      __PARAM_VIRTUALONTOID__		=> variable_get('NCBO_PARAM_ISVIRTUALONTOID', 'true'),
      __PARAM_SEMANTICTYPES__		=> variable_get('NCBO_PARAM_SEMANTICTYPES', ''),
      __PARAM_LEVELMAX__		=> variable_get('NCBO_PARAM_LEVELMAX', '0'),
      __PARAM_MAPPINGTYPES__		=> variable_get('NCBO_PARAM_MAPPINGTYPES', ''),
      __PARAM_FORMAT__			=> variable_get('NCBO_PARAM_FORMAT', 'xml'),
      __PARAM_APIKEY__			=> variable_get('NCBO_APIKEY', ''),
      __PARAM_ONTOTOEXPAND__		=> '',
      __PARAM_ONTOTOKEEP__		=> '',
      __PARAM_TEXTTOANNOTATE__		=> 'virus',
    );

    $data_enc = http_build_query($options, '', '&');
    $params = array(
      'method' 	=> 'POST',
      'data'	=> $data_enc
    );

    $ret = drupal_http_request($uri, $params);
    return $ret;
  }

  public function getOntologies($onto_array) {
    $cached_ontologies = variable_get('NCBO_CACHED_ONTOLOGIES');

    if ($onto_array == NULL || $cached_ontologies == NULL) {
      return '';
    }

    $result = '';
    $onto_of_interest = array();
    $count = 0;

    foreach ($onto_array as $onto_element) {
      foreach ($cached_ontologies as $ontology) {
	if ($ontology['id'] == $onto_element) {
	  $onto_of_interest[$count++] = $ontology;
	}
      }
    }

    if (variable_get('NCBO_PARAM_ISVIRTUALONTOID') == 'true') {
      foreach ($onto_of_interest as $ontology) {
	$result = $result . $ontology['virtualOntologyId'] . ',';
      }
    } else {
      foreach ($onto_of_interest as $ontology) {
	$result = $result . $ontology['localOntologyId'] . ',';
      }
    }

    if ($result != '') {
      $result = substr($result, 0, strlen($result) - 1);
    }
    return $result;
  }

}