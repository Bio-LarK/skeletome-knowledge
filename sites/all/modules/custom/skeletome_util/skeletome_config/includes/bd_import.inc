<?php

define('__BONE_DYSPLASIA_CLASS__', 'http://purl.org/skeletome/bonedysplasia#Bone_Dysplasia');

define('__RDFS_SUBCLASSOF__', 'http://www.w3.org/2000/01/rdf-schema#subClassOf');
define('__RDFS_LABEL__', 'http://www.w3.org/2000/01/rdf-schema#label');
define('__RDFS_SEEALSO__', 'http://www.w3.org/2000/01/rdf-schema#seeAlso');
define('__RDF_FIRST__', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first');
define('__RDF_REST__', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest');
define('__RDF_NIL__', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil');

define('__SKOS_ALTLABEL__', 'http://www.w3.org/2004/02/skos/core#altLabel');

define('__OWL_ALLVALUESFROM__', 'http://www.w3.org/2002/07/owl#allValuesFrom');
define('__OWL_SOMEVALUESFROM__', 'http://www.w3.org/2002/07/owl#someValuesFrom');
define('__OWL_ONPROPERTY__', 'http://www.w3.org/2002/07/owl#onProperty');
define('__OWL_UNIONOF__', 'http://www.w3.org/2002/07/owl#unionOf');
define('__OWL_EQUIVALENTCLASS__', 'http://www.w3.org/2002/07/owl#equivalentClass');

define('__BD_LOCUS__', 'http://purl.org/skeletome/bonedysplasia#locus');
define('__BD_OMIM__', 'http://purl.org/skeletome/bonedysplasia#omim_no');
define('__BD_MESH_ID__', 'http://purl.org/skeletome/bonedysplasia#mesh_id');

define('__BD_MODE_OF_INHERITANCE__', 'http://purl.org/skeletome/bonedysplasia#mode_of_inheritance');
define('__BD_CHARACTERIZED_BY__', 'http://purl.org/skeletome/bonedysplasia#characterized_by');

define('__BONE_DYSPLASIA_GROUP_CONTENTTYPE__', 'bone_dysplasia_group');
define('__BONE_DYSPLASIA_CONTENTTYPE__', 'bone_dysplasia');

function import_bds($indexed_triples)
{
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

    $boneDysplasiaGroupURIs = array();
    $validBoneDysplasiaRootClass = FALSE;

    foreach ($indexed_triples as $indx) {
        if ($indx['p'] == __RDFS_SUBCLASSOF__) {
            if ($indx['o'] == __BONE_DYSPLASIA_CLASS__) {
                $boneDysplasiaGroupURIs[] = $indx['s'];
                $validBoneDysplasiaRootClass = TRUE;
            }
        }
    }

    if (!$validBoneDysplasiaRootClass) {
        drupal_set_message('Bone Dysplasia root class missing. Please ensure you have a valid Bone Dysplasia ontology.', 'error');
        return;
    }

    $uniqueBoneDysplasiaGroupURIs = array_unique($boneDysplasiaGroupURIs);
    $boneDysplasiaInstances = 0;

    $term_uris = _get_term_uris();
    $csterm_uris = _ki_get_csterm_uris();
    $gene_mutation_nids = _get_genemutation_nids();
//    $bd_group_nids = _create_bdgroups($uniqueBoneDysplasiaGroupURIs, $indexed_triples, $data_spo);

    $bd_group_tags_tids = _create_bdgroups($uniqueBoneDysplasiaGroupURIs, $indexed_triples, $data_spo);

    $total_count = 0;
    foreach ($bd_group_tags_tids as $bd_group_uri => $bd_group_nid) {
        $bd_nids = _create_bds($bd_group_uri, $bd_group_nid, $indexed_triples, $gene_mutation_nids, $term_uris, $csterm_uris, $data_spo);
        $total_count += count($bd_nids);
//        $bd_group_node = node_load($bd_group_nid);
//        $count = 0;
//        foreach ($bd_nids as $bd_nid) {
//            $bd_group_node->field_bdgroup_bonedysplasia[LANGUAGE_NONE][$count++]['target_id'] = $bd_nid;
//        }
//        node_save($bd_group_node);

    }

//    $bd_nids = _get_bd_nids();
//    $bdg_nids = _get_bdg_nids();
//
//    _find_equivalences($bd_nids, $data_spo);
//    _find_seeAlso($bd_nids, $bdg_nids, $data_spo);

    drupal_set_message('Total number of Bone Dysplasias imported: ' . $total_count);
}

function _find_equivalences($bd_nids, $data_spo)
{
    foreach ($bd_nids as $bd_uri => $bd_nid) {
        if (array_key_exists(__OWL_EQUIVALENTCLASS__, $data_spo[$bd_uri])) {
            $eq_uris = $data_spo[$bd_uri][__OWL_EQUIVALENTCLASS__];
            $bd_node = node_load($bd_nid);

            $count = 0;
            foreach ($eq_uris as $eq_uri) {
                $eq_nid = $bd_nids[$eq_uri];
                $bd_node->field_bd_sameas[LANGUAGE_NONE][$count++]['target_id'] = $eq_nid;
                $eq_node = node_load($eq_nid);
                $eq_count = 0;
                if (array_key_exists('und', $eq_node->field_bd_sameas)) {
                    $eq_count = count($eq_node->field_bd_sameas[LANGUAGE_NONE]);
                }
                $eq_node->field_bd_sameas[LANGUAGE_NONE][$eq_count]['target_id'] = $bd_nid;
                node_save($eq_node);
            }

            node_save($bd_node);
        }
    }

}

function _find_seeAlso($bd_nids, $bdg_nids, $data_spo)
{
    foreach ($bdg_nids as $bdg_uri => $bdg_nid) {
        if (array_key_exists(__RDFS_SEEALSO__, $data_spo[$bdg_uri])) {
            $seealso_uris = $data_spo[$bdg_uri][__RDFS_SEEALSO__];
            $bdg_node = node_load($bdg_nid);

            $count = 0;
            foreach ($seealso_uris as $seealso_uri) {
                $seealso_nid = NULL;
                if (array_key_exists($seealso_uri, $bdg_nids)) {
                    $seealso_nid = $bdg_nids[$seealso_uri];
                } else {
                    if (array_key_exists($seealso_uri, $bd_nids)) {
                        $seealso_nid = $bd_nids[$seealso_uri];
                    }
                }

                if ($seealso_nid != NULL) {
                    $bdg_node->field_bdgroup_seealso[LANGUAGE_NONE][$count++]['target_id'] = $seealso_nid;
                }
            }
            node_save($bdg_node);
        }
    }

    foreach ($bd_nids as $bd_uri => $bd_nid) {
        if (array_key_exists(__RDFS_SEEALSO__, $data_spo[$bd_uri])) {
            $seealso_uris = $data_spo[$bd_uri][__RDFS_SEEALSO__];
            $bd_node = node_load($bd_nid);

            $count = 0;
            foreach ($seealso_uris as $seealso_uri) {
                $seealso_nid = NULL;
                if (array_key_exists($seealso_uri, $bdg_nids)) {
                    $seealso_nid = $bdg_nids[$seealso_uri];
                } else {
                    if (array_key_exists($seealso_uri, $bd_nids)) {
                        $seealso_nid = $bd_nids[$seealso_uri];
                    }
                }

                if ($seealso_nid != NULL) {
                    $bd_node->field_bd_seealso[LANGUAGE_NONE][$count++]['target_id'] = $seealso_nid;
                }
            }
            node_save($bd_node);
        }
    }
}

function _create_bdgroups($uniqueBoneDysplasiaGroupURIs, $indexed_triples, $data_spo)
{

    $bd_group_nids = array();

    // Delete existing tags
    // TODO: Remove this
    $group_tags_taxonomy = taxonomy_vocabulary_machine_name_load('sk_group_tag');

    $sql_query  = 'SELECT tid FROM {taxonomy_term_data} t WHERE t.vid = :vid';
    $result = db_query($sql_query, array(':vid' => $group_tags_taxonomy->vid));
    // Delete all terms
    foreach ($result as $row) {
        taxonomy_term_delete($row->tid);
    }


    /* Create a Source */
    $group_source_taxonomy = taxonomy_vocabulary_machine_name_load('sk_group_source');
    $group_source_term = array(
        'vid'               => $group_source_taxonomy->vid, // Vocabulary ID
        'name'              => "BDO",
        'description'       => "",
        'sk_gs_field_uri' => array(
            'und' => array(
                array('value' => "http://www.skeletome.org/")
            )
        )
    );
    $group_source_term = (object)$group_source_term;
    taxonomy_term_save($group_source_term);

    /* Create a Source Release */
    $timestamp = 1312157780;
    $group_source_release_taxonomy = taxonomy_vocabulary_machine_name_load('sk_group_source_release');
    $group_source_release_term = array(
        'vid'               => $group_source_release_taxonomy->vid, // Vocabulary ID
        'name'              => "BDO " . date("Y", $timestamp),
        'description'       => "",
        'sk_gs_field_uri' => array(
            'und' => array(
                array('value' => "http://www.skeletome.org/")
            )
        ),
        'sk_gsr_field_timestamp' => array(
            'und' => array(
                array('value' => $timestamp . "")
            )
        ),
        'sk_gsr_field_group_source' => array(
            'und' => array(
                array('tid' => $group_source_term->tid)
            )
        )
    );
    $group_source_release_term = (object)$group_source_release_term;
    taxonomy_term_save($group_source_release_term);

    /* Create the actual groups */
    foreach ($uniqueBoneDysplasiaGroupURIs as $uniqueBoneDysplasiaGroupURI) {

        /* Create group names if they dont already exist */
        $group_name_taxonomy = taxonomy_vocabulary_machine_name_load('sk_group_name');
        $sql = "SELECT tid
                FROM {taxonomy_term_data}
                WHERE vid = :vid
                AND name = :name
                LIMIT 10";

        $group_name_tid = db_query($sql, array(
            'vid'       => $group_name_taxonomy->vid,
            'name'      => $data_spo[$uniqueBoneDysplasiaGroupURI][__RDFS_LABEL__][0]
        ))->fetchField();

        // Group doesn't exist, so create a new one
        if(!$group_name_tid) {
            $term = array(
                'vid' => $group_name_taxonomy->vid, // Vocabulary ID
                'name' => $data_spo[$uniqueBoneDysplasiaGroupURI][__RDFS_LABEL__][0], // Term Name
                'description' => $data_spo[$uniqueBoneDysplasiaGroupURI][__RDFS_LABEL__][0],
            );
            $term = (object)$term;
            taxonomy_term_save($term);
            $group_name_tid = $term->tid;
        }


        // Create a tag


        $group_tags_term = array(
            'vid'               => $group_tags_taxonomy->vid, // Vocabulary ID
            'name'              => $data_spo[$uniqueBoneDysplasiaGroupURI][__RDFS_LABEL__][0] . ' ' . $group_source_release_term->name, // Term Name
            'description'       => "",
            'sk_gt_field_uri'   => array(
                'und' => array(
                    array('value' => $uniqueBoneDysplasiaGroupURI)
                )
            ),
            'sk_gt_field_group_source_release' => array(
                'und' => array(
                    array('tid' =>  $group_source_release_term->tid)
                )
            ),
            'sk_gt_field_group_name' => array(
                'und' => array(
                    array('tid' =>  $group_name_tid)
                )
            )
        );
        $group_tags_term = (object)$group_tags_term;
        taxonomy_term_save($group_tags_term);


//        $bdg_node = new stdClass();
//        $bdg_node->type = __BONE_DYSPLASIA_GROUP_CONTENTTYPE__;
//        $bdg_node->language = 'und';
//        $bdg_node->uid = 1;
//        $bdg_node->field_bdgroup_uri[LANGUAGE_NONE][0]['value'] = $uniqueBoneDysplasiaGroupURI;
//        $bdg_node->title = $data_spo[$uniqueBoneDysplasiaGroupURI][__RDFS_LABEL__][0];
//
//        node_save($bdg_node);
//        $bd_group_nids[$uniqueBoneDysplasiaGroupURI] = $bdg_node->nid;

        $bd_group_tags[$uniqueBoneDysplasiaGroupURI] = $group_tags_term->tid;
    }

//    drupal_set_message('Total number of Bone Dysplasia Groups imported: ' . count($bd_group_nids));
    drupal_set_message('Total number of Bone Dysplasia Groups imported: ' . count($bd_group_tags));
//    return $bd_group_nids;
    return $bd_group_tags;
}

function _create_bds($bd_group_uri, $bd_group_nid, $indexed_triples, $gene_mutation_nids, $term_uris, $csterm_uris, $data_spo)
{
    $bd_nids = array();

    foreach ($indexed_triples as $indx) {
        if ($indx['p'] == __RDFS_SUBCLASSOF__ && $indx['o'] == $bd_group_uri) {
            $bd_nid = _create_bd($indx['s'], $bd_group_nid, $bd_group_uri, $indexed_triples, $gene_mutation_nids, $term_uris, $csterm_uris, $data_spo);

            foreach ($bd_nid as $idx) {
                $bd_nids[] = $idx;
            }
        }
    }

    return $bd_nids;
}

function _create_bd($bd_uri, $bd_group_nid, $bd_group_uri, $indexed_triples, $gene_mutation_nids, $term_uris, $csterm_uris, $data_spo)
{
    $bd_node = new stdClass();
    $bd_node->type = __BONE_DYSPLASIA_CONTENTTYPE__;
    $bd_node->uid = 1;
    $bd_node->field_bd_uri[LANGUAGE_NONE][0]['value'] = $bd_uri;
//    $bd_node->field_bd_group[LANGUAGE_NONE][0]['target_id'] = $bd_group_nid;

    $bd_node->sk_bd_tags[LANGUAGE_NONE][]['tid'] = $bd_group_nid;


    $altLabel_count = 0;
    $restrictions = array();
    $subclasses = array();

    $predicates = $data_spo[$bd_uri];
    $bd_node->title = $predicates[__RDFS_LABEL__][0];
    if (array_key_exists(__SKOS_ALTLABEL__, $predicates)) {
        $objs = $predicates[__SKOS_ALTLABEL__];
        $altLabel_count = 0;
        foreach ($objs as $obj) {
            $bd_node->field_bd_synonym[LANGUAGE_NONE][$altLabel_count++]['value'] = $obj;
        }
    }

    if (array_key_exists(__BD_OMIM__, $predicates)) {
        $bd_node->field_bd_omim[LANGUAGE_NONE][0]['value'] = $predicates[__BD_OMIM__][0];
    }

    if (array_key_exists(__BD_MESH_ID__, $predicates)) {
        $meshs = $predicates[__BD_MESH_ID__];
        $count = 0;
        foreach ($meshs as $mesh) {
            $bd_node->field_bd_mesh[LANGUAGE_NONE][$count++]['value'] = $mesh;
        }
    }

    if (array_key_exists(__RDFS_SUBCLASSOF__, $predicates)) {
        $objs = $predicates[__RDFS_SUBCLASSOF__];
        foreach ($objs as $obj) {
            if ($obj != __BONE_DYSPLASIA_CLASS__ && $obj != $bd_group_uri) {
                $restrictions[] = $obj;
            }
        }
    }
    $subclasses = _find_subclasses($bd_uri, $indexed_triples);

    node_save($bd_node);

//    $xray_nid = _create_xraycollection($bd_node);
//    $bd_node->field_bd_xrays['und'][0]['target_id'] = $xray_nid;
//    node_save($bd_node);

    $nids_to_return = array();
    $nids_to_return[] = $bd_node->nid;

    // sub-types
    $count = 0;
    foreach ($subclasses as $subclass_uri) {
        $subclass_nid = _create_subclass($subclass_uri, $bd_uri, $bd_node->nid, $bd_group_nid, $indexed_triples, $gene_mutation_nids, $term_uris, $csterm_uris, $data_spo);
        $bd_node->field_bd_subbd[LANGUAGE_NONE][$count++]['target_id'] = $subclass_nid;
        $nids_to_return[] = $subclass_nid;
    }
    node_save($bd_node);

    $moi_uris = _find_moi_uris($restrictions, $indexed_triples, $data_spo);
    $moi_tids = _find_moi_tids($moi_uris, $term_uris);
    $count = 0;
    foreach ($moi_tids as $moi_tid) {
        $bd_node->field_bd_moi[LANGUAGE_NONE][$count++]['tid'] = $moi_tid;
    }
    node_save($bd_node);

    $gm_nids = _find_restrictions($restrictions, $indexed_triples, $gene_mutation_nids, $data_spo, $bd_node, $csterm_uris);
    $count = 0;
    foreach ($gm_nids as $gm_nid) {
        $bd_node->field_bd_gm[LANGUAGE_NONE][$count++]['target_id'] = $gm_nid;
        $gm_node = node_load($gm_nid);
        $gm_node->field_gene_mutation_bd[LANGUAGE_NONE][0]['target_id'] = $bd_node->nid;
        node_save($gm_node);
    }
    node_save($bd_node);

    return $nids_to_return;
}

function _find_restrictions($restrictions, $indexed_triples, $gene_mutation_nids, $data_spo, $bd_node, $csterm_uris)
{
    $gm_nids = array();
    $res_uris = array();

    $phenores_uris = array();

    foreach ($restrictions as $bnode) {
        $predicates = $data_spo[$bnode];
        $onProperty = $predicates[__OWL_ONPROPERTY__][0];

        if ($onProperty == __BD_CHARACTERIZED_BY__) {
            $bnodes = array();

            if (array_key_exists(__OWL_ALLVALUESFROM__, $predicates)) {
                if (array_key_exists($predicates[__OWL_ALLVALUESFROM__][0], $gene_mutation_nids)) {
                    $res_uris[] = $predicates[__OWL_ALLVALUESFROM__][0];
                } else {
                    foreach ($predicates[__OWL_ALLVALUESFROM__] as $bbnode) {
                        $bnodes[] = $bbnode;
                    }
                }
            }

            foreach ($bnodes as $bbnode) {
                $bpreds = $data_spo[$bbnode];
                if (array_key_exists(__OWL_UNIONOF__, $bpreds)) {
                    $union = $bpreds[__OWL_UNIONOF__][0];

                    $upreds = $data_spo[$union];
                    if (array_key_exists(__RDF_FIRST__, $upreds)) {
                        $tmpres_uris = _explode_collection($upreds, $data_spo);
                        foreach ($tmpres_uris as $tmp_resuri) {
                            if (array_key_exists($tmp_resuri, $gene_mutation_nids)) {
                                // GENE MUTATIONS
                                $res_uris[] = $tmp_resuri;
                            } else {
                                // PHENOTYPIC CHARACTERISTICS
                                $phenores_uris[] = $tmp_resuri;
                            }
                        }
                    }
                }
            }
        }
    }

    $res_uris = array_unique($res_uris);
    $phenores_uris = array_unique($phenores_uris);

    _add_pheno_chars($phenores_uris, $bd_node, $csterm_uris);

    foreach ($res_uris as $res_uri) {
        $gm_nids[] = $gene_mutation_nids[$res_uri];
    }

    return $gm_nids;
}

function _explode_collection($preds, $data_spo)
{
    $gm_array = array();
    $gm_array[] = $preds[__RDF_FIRST__][0];
    if ($preds[__RDF_REST__][0] == __RDF_NIL__) {
        return $gm_array;
    } else {
        $gm_array_tmp = _explode_collection($data_spo[$preds[__RDF_REST__][0]], $data_spo);
        foreach ($gm_array_tmp as $gm_tmp) {
            $gm_array[] = $gm_tmp;
        }
    }

    return $gm_array;
}

function _add_pheno_chars($phenores_uris, $bd_node, $csterm_uris)
{
    $pheno_tids = _find_pheno_tids($phenores_uris, $csterm_uris);
    $count = 0;
    foreach ($pheno_tids as $pheno_tid) {
        $bd_node->field_skeletome_tags[LANGUAGE_NONE][$count++]['tid'] = $pheno_tid;
    }
    node_save($bd_node);

}

function _find_moi_uris($restrictions, $indexed_triples, $data_spo)
{
    $moi_uris = array();

    foreach ($restrictions as $bnode) {
        $predicates = $data_spo[$bnode];
        $onProperty = $predicates[__OWL_ONPROPERTY__][0];

        if ($onProperty == __BD_MODE_OF_INHERITANCE__) {
            if (array_key_exists(__OWL_ALLVALUESFROM__, $predicates)) {
                $moi_uris[] = $predicates[__OWL_ALLVALUESFROM__][0];
            } else {
                if (array_key_exists(__OWL_SOMEVALUESFROM__, $predicates)) {
                    $moi_uris[] = $predicates[__OWL_SOMEVALUESFROM__][0];
                }
            }
        }
    }

    $moi_uris = array_unique($moi_uris);
    return $moi_uris;
}

function _find_moi_tids($moi_uris, $term_uris)
{
    $moi_tids = array();

    foreach ($moi_uris as $moi_uri) {
        if (array_key_exists($moi_uri, $term_uris)) {
            $moi_tids[] = $term_uris[$moi_uri];
        }
    }

    return $moi_tids;
}

function _find_pheno_tids($phenores_uris, $csterm_uris)
{
    $pheno_tids = array();

    foreach ($phenores_uris as $phenores_uri) {
        if (array_key_exists($phenores_uri, $csterm_uris)) {
            $pheno_tids[] = $csterm_uris[$phenores_uri];
        }
    }

    return $pheno_tids;
}

function _find_subclasses($bd_uri, $indexed_triples)
{
    $subclasses = array();
    foreach ($indexed_triples as $indx) {
        if ($indx['p'] == __RDFS_SUBCLASSOF__ && $indx['o'] == $bd_uri) {
            $subclasses[] = $indx['s'];
        }
    }
    return $subclasses;
}

function _create_subclass($bd_uri, $parent_uri, $parent_nid, $bd_group_nid, $indexed_triples, $gene_mutation_nids, $term_uris, $csterm_uris, $data_spo)
{
    $bd_node = new stdClass();
    $bd_node->type = __BONE_DYSPLASIA_CONTENTTYPE__;
    $bd_node->uid = 1;
    $bd_node->field_bd_uri[LANGUAGE_NONE][0]['value'] = $bd_uri;
//    $bd_node->field_bd_group[LANGUAGE_NONE][0]['target_id'] = $bd_group_nid;
    $bd_node->sk_bd_tags[LANGUAGE_NONE][]['tid'] = $bd_group_nid;

    $bd_node->field_bd_superbd[LANGUAGE_NONE][0]['target_id'] = $parent_nid;

    $altLabel_count = 0;
    $restrictions = array();

    $predicates = $data_spo[$bd_uri];
    $bd_node->title = $predicates[__RDFS_LABEL__][0];
    if (array_key_exists(__SKOS_ALTLABEL__, $predicates)) {
        $objs = $predicates[__SKOS_ALTLABEL__];
        $altLabel_count = 0;
        foreach ($objs as $obj) {
            $bd_node->field_bd_synonym[LANGUAGE_NONE][$altLabel_count++]['value'] = $obj;
        }
    }
    if (array_key_exists(__BD_OMIM__, $predicates)) {
        $bd_node->field_bd_omim[LANGUAGE_NONE][0]['value'] = $predicates[__BD_OMIM__][0];
    }

    if (array_key_exists(__BD_MESH_ID__, $predicates)) {
        $meshs = $predicates[__BD_MESH_ID__];
        $count = 0;
        foreach ($meshs as $mesh) {
            $bd_node->field_bd_mesh[LANGUAGE_NONE][$count++]['value'] = $mesh;
        }
    }

    if (array_key_exists(__RDFS_SUBCLASSOF__, $predicates)) {
        $objs = $predicates[__RDFS_SUBCLASSOF__];
        foreach ($objs as $obj) {
            if ($obj != __BONE_DYSPLASIA_CLASS__ && $obj != $parent_uri) {
                $restrictions[] = $obj;
            }
        }
    }
    node_save($bd_node);

    $xray_nid = _create_xraycollection($bd_node);
    $bd_node->field_bd_xrays['und'][0]['target_id'] = $xray_nid;
    node_save($bd_node);

    $moi_uris = _find_moi_uris($restrictions, $indexed_triples, $data_spo);
    $moi_tids = _find_moi_tids($moi_uris, $term_uris);
    $count = 0;
    foreach ($moi_tids as $moi_tid) {
        $bd_node->field_bd_moi[LANGUAGE_NONE][$count++]['tid'] = $moi_tid;
    }
    node_save($bd_node);

    $gm_nids = _find_restrictions($restrictions, $indexed_triples, $gene_mutation_nids, $data_spo, $bd_node, $csterm_uris);
    $count = 0;
    foreach ($gm_nids as $gm_nid) {
        $bd_node->field_bd_gm[LANGUAGE_NONE][$count++]['target_id'] = $gm_nid;
        $gm_node = node_load($gm_nid);
        $gm_node->field_gene_mutation_bd[LANGUAGE_NONE][0]['target_id'] = $bd_node->nid;
        node_save($gm_node);
    }
    node_save($bd_node);

    return $bd_node->nid;
}

function _get_term_uris()
{
    $term_uris = array();

    $sql = 'SELECT entity_id, field_moi_term_uri_value FROM {field_data_field_moi_term_uri}';
    $result = db_query($sql, array());
    foreach ($result as $r) {
        $term_uris[$r->field_moi_term_uri_value] = $r->entity_id;
    }

    return $term_uris;
}

function _ki_get_csterm_uris()
{
    $term_uris = array();

    $sql = 'SELECT entity_id, field_term_uri_value FROM {field_data_field_term_uri}';
    $result = db_query($sql, array());
    foreach ($result as $r) {
        $term_uris[$r->field_term_uri_value] = $r->entity_id;
    }

    return $term_uris;
}

function _get_genemutation_nids()
{
    $gm_nids = array();
    $sql = 'SELECT nid FROM {node} n WHERE n.type = :type';
    $result = db_query($sql, array(':type' => 'gene_mutation'));
    foreach ($result as $row) {
        $gm_node = node_load($row->nid);
        $gm_nids[$gm_node->field_gene_mutation_uri['und'][0]['value']] = $row->nid;
    }

    return $gm_nids;
}

function _get_bdg_nids()
{
    $bdg_nids = array();
    $sql = 'SELECT nid FROM {node} n WHERE n.type = :type';
    $result = db_query($sql, array(':type' => 'bone_dysplasia_group'));
    foreach ($result as $row) {
        $bdg_node = node_load($row->nid);
        $bdg_nids[$bdg_node->field_bdgroup_uri['und'][0]['value']] = $row->nid;
    }

    return $bdg_nids;
}

function _get_bd_nids()
{
    $bd_nids = array();
    $sql = 'SELECT nid FROM {node} n WHERE n.type = :type';
    $result = db_query($sql, array(':type' => 'bone_dysplasia'));
    foreach ($result as $row) {
        $bd_node = node_load($row->nid);
        $bd_nids[$bd_node->field_bd_uri['und'][0]['value']] = $row->nid;
    }

    return $bd_nids;
}

function _create_xraycollection($bd_node)
{
    global $user;

    $xray_node = new stdClass();
    $xray_node->type = 'media_gallery';
    $xray_node->language = 'und';
    $xray_node->title = 'X-Ray collection: ' . $bd_node->title;
    $xray_node->uid = $user->uid;
    $xray_node->media_gallery_bd['und'][0]['target_id'] = $bd_node->nid;
    $xray_node->media_gallery_block_columns[LANGUAGE_NONE][0]['value'] = 4;
    $xray_node->media_gallery_block_rows[LANGUAGE_NONE][0]['value'] = 2;

    node_save($xray_node);

    return $xray_node->nid;
}
