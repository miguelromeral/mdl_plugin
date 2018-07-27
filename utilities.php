<?php

require_once($CFG->dirroot.'/mod/league/classes/model.php');









function getURLFile($contextid, $component, $filearea, $itemid, $name){
    global $CFG;
    
    $url = $CFG->wwwroot;
    $url .= "/pluginfile.php/";
    $url .= ($contextid)."/";
    $url .= ($component)."/";
    $url .= ($filearea)."/";
    $url .= ($itemid)."/";
    $url .= $name;
    return $url;
}


function restoreURLFile($contextid, $itemid){
    $component = 'mod_league';
    $filearea = 'exuplod';
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
        foreach ($files as $file) {
            $contenthash = $file->get_contenthash();
            $id_file = \league_model::getIDFileFromContenthash($contenthash);


            $url = getURLFile($file->get_contextid(), $file->get_component(), 
                    $file->get_filearea(), $file->get_itemid(), $file->get_filename());

            $resultado = new stdClass();
            $resultado->id = $id_file;
            $resultado->url = $url;
            return $resultado;
        }
    }
    return null;
}

/*

function cmp($a, $b)
{
    return strcmp($a->nombre, $b->nombre);
}*/


function generateRandomFileID(){
    $min = 1;
    $max = 10000000000;
    $encontrado = false;
    $r = -1;
    while(! $encontrado){
        $encontrado = true;
        $r = rand($min, $max);
        global $DB;
        $var="select distinct id_file
        from mdl_league_attempt";
        $data = $DB->get_records_sql($var);
        foreach ($data as $d){
            $d = get_object_vars($d);
            if($d['id_file'] == $r){
                $encontrado = false;
            }
        } 
    }
    return $r;
}

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
