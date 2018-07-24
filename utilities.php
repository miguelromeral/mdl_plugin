<?php

function get_students(){
    global $COURSE, $DB;
    $cContext = context_course::instance($COURSE->id);
    $query = 'select u.id as id, firstname, lastname, picture, imagealt, '
            . 'email, u.* from mdl_role_assignments as a, mdl_user as u where '
            . 'contextid=' . $cContext->id . ' and roleid=5 and a.userid=u.id '
            . 'order by firstname desc';
    $rs = $DB->get_recordset_sql( $query );
    return $rs;
}

function is_student($userid){
    $everyone = get_students();
    foreach( $everyone as $r ) {
       if($r->id == $userid){
           return true;
       }
    }
    return false;
}

function get_editing_users(){
    global $COURSE, $DB;
    $cContext = context_course::instance($COURSE->id);

    $query = 'select u.id as id, firstname, lastname, picture, imagealt, '
            . 'email, u.* from mdl_role_assignments as a, mdl_user as u where '
            . 'contextid=' . $cContext->id . ' and roleid < 4 and a.userid=u.id';
    $rs = $DB->get_recordset_sql( $query );
    return $rs;
}

function is_editing_user($userid){
    $everyone = get_editing_users();
    foreach( $everyone as $r ) {
       if($r->id == $userid){
           return true;
       }
    }
    return false;
}

function get_role_user($userid){
    if(is_student($userid)){
        return 'student';
    }else if(is_editing_user($userid)){
        return 'teacher';
    }else
        return 'nouser';
}

function get_exercises_from_id($idliga){
    global $DB;
    $var="SELECT * 
    FROM mdl_league_exercise
    WHERE league = $idliga
    ORDER BY id";
    $data = $DB->get_records_sql($var);
    
    return $data;
}

function get_exercises_from_id_by_user($idliga, $iduser){
    global $DB;
    $var="SELECT e.*, a.num 
        FROM mdl_league_exercise e
        LEFT JOIN (
                select count(id) as num, exercise
                from mdl_league_attempt
                where id_user = $iduser
                group by exercise
        ) a 
        ON e.id = a.exercise
        WHERE league = $idliga
        ORDER BY id";
    $data = $DB->get_records_sql($var);
    
    return $data;
}

function get_students_exercise($id_exer){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_league_attempt as a
    inner join (
            select max(c.id) as id, count(c.id) as num, c.id_user, d.firstname, d.lastname
            from mdl_league_attempt as c
            inner join mdl_user as d
            on c.id_user = d.id
            where c.exercise = $id_exer
            group by c.id_user
            order by c.id desc
    ) as b
    on a.id = b.id
    group by b.id_user
    order by a.timemodified desc";
    $data = $DB->get_records_sql($var);
    return $data;
}


function get_total_students_exercises($id_exer){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_league_attempt as a
    join (
            select id as us, firstname, lastname
            from mdl_user
    ) as b
    on a.id_user = b.us
    where a.exercise = $id_exer
    order by a.timemodified desc";
    $data = $DB->get_records_sql($var);
    return $data;
}


function getIDFileFromContenthash($contenthash){
    global $DB;
    $var="SELECT max(id) as 'm'
    FROM mdl_files
    WHERE contenthash = '$contenthash'";

    $data = $DB->get_records_sql($var);
    $id = -1;
    foreach ($data as $d){
        //print_r($d);
        foreach($d as $i => $l){
            $id = $l;
        }
    }
    
    return $id;
}

function get_notas_alumno($idleague, $cmid, $userid, $contextid){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_league_exercise as a
    left outer join
    (
        select a.id as idat, a.timemodified as tma,
		a.observations, a.name as fname,
		a.exercise, b.id_user, a.mark, a.id_file, a.url
		from mdl_league_attempt as a
		inner join (
			select max(id) as m, id_user
			from mdl_league_attempt
			where id_user = $userid
			group by exercise
		) as b
		on a.id = b.m
    ) as b
    on a.id = b.exercise
    where a.league = $idleague";
    $data = $DB->get_records_sql($var);
    
    return $data;
}

function get_qualy_array($idleague, $idcurso, $rol, $method){
    global $DB;
    //Lista de estudiantes de un curso
    $var="SELECT DISTINCT u.id AS userid, c.id AS courseid, u.firstname, u.lastname, u.username
    FROM mdl_user u
    JOIN mdl_user_enrolments ue ON ue.userid = u.id
    JOIN mdl_enrol e ON e.id = ue.enrolid
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
    JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id
    JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student'
    WHERE e.status = 0 AND u.suspended = 0 AND u.deleted = 0
      AND (ue.timeend = 0 OR ue.timeend > NOW()) AND ue.status = 0 and c.id = $idcurso";
    $data = $DB->get_records_sql($var);
    $q = Array();
    foreach ($data as $d){
        $d = get_object_vars($d);
        $var2 = "select count(id) as te, count(idat) as eu, sum(mark) as acum, COUNT(CASE WHEN mark = -1 THEN 1 END) as sc
        from mdl_league_exercise as a
        left outer join
        (
            select a.id as idat, a.timemodified as tma,
                    a.observations, a.name as fname,
                    a.exercise, b.id_user, a.mark, a.id_file, a.url
                    from mdl_league_attempt as a
                    inner join (
                            select max(id) as m, id_user
                            from mdl_league_attempt
                            where id_user = ${d['userid']}
                            group by exercise
                    ) as b
                    on a.id = b.m
        ) as b
        on a.id = b.exercise
        where a.league = $idleague";
        if($rol == 'student'){
            $var2 .= " and a.published = 1";
        }
        $data2 = $DB->get_records_sql($var2);
        foreach ($data2 as $d2){
            $d2 = get_object_vars($d2);
            $fila = Array();
            $fila += array('name' => $d['firstname']." ".$d['lastname']);
            $fila += array('uname' => $d['username']);
            $fila += array('uid' => $d['userid']);
            $fila += array('totalexer' => $d2['te']);
            $fila += array('exeruplo' => $d2['eu']);
            $fila += array('totalmark' => $d2['acum'] + $d2['sc']);
            $fila += array('marks' => getArrayMarkByStudent($idleague, $d['userid'], true));
            $fila += array('notes' => "");
        }
        array_push($q, $fila);
    }
    
    switch($method){
        case 1: return sort_qualy_array_best_marks($q);
        case 2: return sort_qualy_array_more_exercises($q);
        default: return $q;
    }
}

function exchange($array, $id1, $id2){
    $aux = $array[$id1];
    $array[$id1] = $array[$id2];
    $array[$id2] = $aux;
    return $array;
}

function sort_qualy_array_best_marks($q){
    $n = sizeof($q);
    //Algoritmo burbuja
    for ($i = 1; $i < $n; $i++){
        for($j = 0; $j < $n - $i; $j++){
            $r1 = $q[$j];
            $r2 = $q[$j+1];
            //echo "<br> Miro ".$j." y ".($j+1)." ( ${r1['totalmark']} / ${r2['totalmark']}) <br>";
            if($r2['totalmark'] > $r1['totalmark'] || 
                    ($r2['totalmark'] === $r1['totalmark'] && mejoresNotasSegundo($q, $r1, $r2))){
               // echo "<br>CAMBIO<br>";
                $q = exchange($q, $j, $j+1);
            }
        }
       }
    //Ya está ordenado, ahora a poner las aclaraciones en caso de empates
    for ($i = 0; $i < $n - 1; $i++){
        $r1 = $q[$i];
        $r2 = $q[$i+1];
        $q = setNotesBM($q, $r1, $r2, $i, $i+1);
    }
    return $q;
}

function sort_qualy_array_more_exercises($q){
    $n = sizeof($q);
    //Algoritmo burbuja
    for ($i = 1; $i < $n; $i++){
        for($j = 0; $j < $n - $i; $j++){
            $r1 = $q[$j];
            $r2 = $q[$j+1];
            //echo "<br> Miro ".$j." y ".($j+1)." ( ${r1['totalmark']} / ${r2['totalmark']}) <br>";
            if($r2['exeruplo'] > $r1['exeruplo'] ||
                    ($r2['exeruplo'] === $r1['exeruplo'] && 
                        ($r2['totalmark'] > $r1['totalmark']))){
                //echo "<br>CAMBIO<br>";
                $q = exchange($q, $j, $j+1);
            }
        }
       }
    //Ya está ordenado, ahora a poner las aclaraciones en caso de empates
    for ($i = 0; $i < $n - 1; $i++){
        $r1 = $q[$i];
        $r2 = $q[$i+1];
        $q = setNotesME($q, $r1, $r2, $i, $i+1);
    }
    return $q;
}

function setNotesBM($q, $r1, $r2, $f, $s){
    $aux = 0;
    $s = $r1['exeruplo'];
    if($r1['exeruplo'] > $r2['exeruplo'] && $r1['totalmark'] === $r2['totalmark']){
        $q[$f]['notes'] = get_string('more_exercises_uploaded','league');
    }else{
        if($r1['totalmark'] === $r2['totalmark']){
            while (true) {
                if($s != $aux){
                    $n1 = $r1['marks'][$aux];
                    $n2 = $r2['marks'][$aux];
                    if($n1 && $n2){
                        if($n1 > $n2){
                            $q[$f]['notes'] = get_string('higher_mark','league').' '. comparaNotas($q, $f, $s, true).' a '
                                    . comparaNotas($q, $f, $s, false);
                            return $q;
                        }
                        if($n1 == $n2){
                            $aux += 1;
                        }else{
                            if($n2 == get_string('q_tba', 'league')){
                                //$q[$f]['notes'] = "REVISAR";
                                return $q;
                            }
                        }
                    }else{
                        $q[$f]['notes'] = get_string('total_draw','league');
                        return $q;
                    }
                }else {
                    $q[$f]['notes'] = get_string('total_draw','league');
                    return $q;
                }
            }
        }
    }
    return $q;
}
function setNotesME($q, $r1, $r2, $f, $s){
    
    $aux = 0;
    $s = $r1['exeruplo'];
    if($r1['exeruplo'] != $r2['exeruplo']){
        if($r1['exeruplo'] > $r2['exeruplo']){
            $q[$f]['notes'] = get_string('more_exercises_uploaded','league');
        }
    }else{
        if($r1['totalmark'] === $r2['totalmark']){
            while (true) {
                if($s != $aux){
                    $n1 = $r1['marks'][$aux];
                    $n2 = $r2['marks'][$aux];
                    if($n1 && $n2){
                        if($n1 > $n2){
                            $q[$f]['notes'] = get_string('higher_mark','league').' '. comparaNotas($q, $f, $s, true).' - '
                                    . comparaNotas($q, $f, $s, false);
                            return $q;
                        }
                        if($n1 == $n2){
                            $aux += 1;
                        }else{
                            if($n2 == get_string('q_tba', 'league')){
                                //$q[$f]['notes'] = "REVISAR";
                                return $q;
                            }
                        }
                    }else{
                        $q[$f]['notes'] = get_string('total_draw','league');
                        return $q;
                    }
                }else{
                    $q[$f]['notes'] = get_string('total_draw','league');
                    return $q;
                }
            } 
        }
    }
    return $q;
}

function comparaNotas($q, $i, $j, $primero){
    $notas1 = $q[$i]['marks'];
    $notas2 = $q[$j]['marks'];
    //$ant1 = -1;
    //$ant2 = -1;
    $i = 0;
    while (true) {
        $n1 = (isset($notas1[$i]) ? $notas1[$i] : -1);
        $n2 = (isset($notas2[$i]) ? $notas2[$i] : -1);
        if($n2 != $n1){
            return ($primero ? $n1 : $n2);
        }
        if($n1 == $n2){
            if($n1 == -1){
                return get_string('q_tba', 'league');
                //return ($primero ? $ant1 : $ant2);
            }
            //$ant1 = $n1;
            //$ant2 = $n2;
            $i += 1;
        }
    }
}

// TRUE si r2 tiene mejores notas
function mejoresNotasSegundo($q, $r1, $r2){
    $i = 0;
    $s = max($r1['exeruplo'],$r2['exeruplo']);
    if($r1['exeruplo'] != $r2['exeruplo']){
        if($r2['exeruplo'] > $r1['exeruplo']){
            return true;
        }
    }else{
        while (true) {
            if($i != $s){
                $n1 = ($r1['marks'][$i] ? $r1['marks'][$i] : null);
                $n2 = ($r2['marks'][$i] ? $r2['marks'][$i] : null);
                if($n1 && $n2){
                    if($n2 > $n1){
                        return true;
                    }
                    if($n1 > $n2){
                        return false;
                    }
                    if($n1 == $n2){
                        $i += 1;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
}

function getArrayMarkByStudent($idleague, $iduser, $toprint){
    global $DB;
    //Lista de estudiantes de un curso
    $var="select a.id, b.mark, a.published
            from mdl_league_exercise as a
            left outer join
            (
                select a.id as idat, a.timemodified as tma,
                        a.observations, a.name as fname,
                        a.exercise, b.id_user, a.mark, a.id_file, a.url
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
            where a.league = $idleague
    order by mark desc";
    $data = $DB->get_records_sql($var);
    $mark = Array();
    foreach ($data as $d){
        $d = get_object_vars($d);
        if ($d['mark'] != -1){
            if($toprint || $d['published'] == 1){
                array_push($mark, $d['mark']);
            }
        }else{
            if($toprint){
                array_push($mark, get_string('q_tba','league'));
            }
        }
    }
    return $mark;
}

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
            $id_file = getIDFileFromContenthash($contenthash);


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
		a.exercise, b.id_user, a.mark, a.id_file, a.url
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
