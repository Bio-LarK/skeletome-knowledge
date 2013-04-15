<?php

function delete_content($to_delete)
{
    if ($to_delete === '0') {
        do_delete('bone_dysplasia');
        do_delete('bone_dysplasia_group');
        delete_taxonomy('sk_group_tag');
        delete_taxonomy('sk_group_name');
        delete_taxonomy('sk_group_source_release');
        delete_taxonomy('sk_group_source');
    }
    if ($to_delete === '1') {
        do_delete('gene_mutation');
        do_delete('gene');
    }
    if ($to_delete === '2') {
        do_delete('gene_mutation_type');
    }
    if ($to_delete === '3') {
        do_delete('bone_dysplasia');
        do_delete('bone_dysplasia_group');
        do_delete('gene_mutation');
        do_delete('gene');
        do_delete('gene_mutation_type');
    }
    if ($to_delete === '4') {
        delete_taxonomy('mode_of_inheritance');
    }
    if ($to_delete === '5') {
        delete_taxonomy('skeletome_vocabulary');
    }
}

function do_delete($content_type)
{
    $sql = 'SELECT nid FROM {node} n WHERE n.type = :type';
    $result = db_query($sql, array(':type' => $content_type));
    foreach ($result as $row) {
        node_delete($row->nid);
    }
}

function delete_taxonomy($vocab_name)
{
    $sql = 'SELECT vid FROM {taxonomy_vocabulary} v WHERE v.machine_name = :machine_name';
    $result = db_query($sql, array(':machine_name' => $vocab_name));
    $vid = array();
    foreach ($result as $row) {
        $vid = $row->vid;
    }

    $sql = 'SELECT tid FROM {taxonomy_term_data} t WHERE t.vid = :vid';
    $result = db_query($sql, array(':vid' => $vid));
    $tids = array();
    foreach ($result as $row) {
        $tids[] = $row->tid;
    }

    foreach ($tids as $tid) {
        taxonomy_term_delete($tid);
    }
}