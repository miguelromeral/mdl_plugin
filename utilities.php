<?php

require_once($CFG->dirroot.'/mod/league/classes/model.php');








function publishedMarks($exercise){
    global $DB;
    //Lista de estudiantes de un curso
    $var="select a.published
        from mdl_league_exercise as a
        where id = $exercise";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        if ($d['published'] == 0){
            return false;
        }else{
            return true;
        }
    }
}

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

function get_notas_alumno_para_profesor($iduser, $idleague){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_league_exercise as a
    left outer join
    (
        select a.id as idat, a.timemodified as tma,
		a.observations, a.name as fname,
		a.exercise, b.id_user, a.mark, a.id_file
		from mdl_league_attempt as a
		inner join (
			select max(id) as m, id_user
			from mdl_league_attempt
			where id_user = $iduser
			group by exercise
		) as b
		on a.id = b.m
    ) as b
    on a.id = b.exercise
    where a.league = $idleague";
    $data = $DB->get_records_sql($var);
    
    $total = array();
    
    foreach ($data as $d){
        $d = get_object_vars($d);
        $notas = new stdClass();
        $notas->exercise = $d['exercise'];
        $notas->mark = $d['mark'];
        array_push($total, $notas);
    }
    return $total;
}

function get_tabla_notas($idliga, $estudiantes){
    $marks = array();
    foreach($estudiantes as $e){
        $fila = new stdClass();
        $fila->id = $e->id;
        $fila->firstname = $e->firstname;
        $fila->lastname = $e->lastname;
        $fila->notas = get_notas_alumno_para_profesor($e->id, $idliga);
        array_push($marks, $fila);
    }
    return $marks;
}

/*
function deleteFileAttempt($contextid, $itemid){
    $component = 'mod_league';
    $filearea = 'exuplod';
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
        foreach ($files as $file) {
            $file->delete();
            return true;
        }
    }
    return false;
}*/
/*
function get_num_attempts_by_exer($idexer, $iduser){
    global $DB;
    //Lista de estudiantes de un curso
    $var="select count(id) as c
    from mdl_league_attempt
    where id_user = $iduser and exercise = $idexer";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        return $d['c'];
    }
    return 0;
}

function attempt_already_sent($idexer, $iduser){
    if(get_num_attempts_by_exer($idexer, $iduser) > 0){
        return true;
    }
    return false;
}*/

function cmp($a, $b)
{
    return strcmp($a->nombre, $b->nombre);
}


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
