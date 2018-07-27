<?php

require_once($CFG->dirroot.'/mod/league/classes/model.php');










/*

function cmp($a, $b)
{
    return strcmp($a->nombre, $b->nombre);
}*/



function getNameExerByID($id, $name = true){
    global $DB;
    $var="select name, statement
    from mdl_league_exercise
    where id = $id";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        return ($name ? $d['name'] : $d['statement']);
    } 
    return null;
}

function isleagueexercise($idexer, $idleague){
    global $DB;
    $var="select id, league
    from mdl_league_exercise
    where id = $idexer";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        if($d['id']){
            return ($d['league'] == $idleague);
        }
    } 
    return null;
}

function getDataFromAttempt($id, $field){
    global $DB;
    $var="select *
    from mdl_league_attempt
    where id = $id";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        if($d[$field]){
            return $d[$field];
        }
    } 
    return null;
}
