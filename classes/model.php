<?php

//namespace mod_league\model;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

class league_model {
    
    public static function get_user_image($iduser, $size){
        global $COURSE, $DB, $OUTPUT;
        $cContext = context_course::instance($COURSE->id);
        $query = 'select u.id as id, firstname, lastname, picture, imagealt, '
                . 'email, u.* from mdl_role_assignments as a, mdl_user as u where '
                . 'contextid=' . $cContext->id . ' and roleid=5 and a.userid=u.id';
        $rs = $DB->get_recordset_sql( $query );
        foreach( $rs as $r ) {
            if($r->id == $iduser){
                return $OUTPUT->user_picture($r, array('courseid'=>$COURSE->id));
                //return $OUTPUT->user_picture($r, array('size' => $size, 'courseid'=>$COURSE->id));
            }
        }
        return null;
    }
    
    public static function get_exercises_from_id($idliga){
        global $DB;
        $var="SELECT * 
        FROM mdl_league_exercise
        WHERE league = $idliga
        ORDER BY id";
        $data = $DB->get_records_sql($var);

        return $data;
    }
    
    public static function get_exercises_from_id_by_user($idliga, $iduser){
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
    /*
    public static function get_students_exercise($id_exer){
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
    }*/
    
    public static function get_total_students_exercises($id_exer){
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
    
    public static function get_students(){
        global $COURSE, $DB;
        $cContext = context_course::instance($COURSE->id);
        $query = 'select u.id as id, firstname, lastname, picture, imagealt, '
                . 'email, u.* from mdl_role_assignments as a, mdl_user as u where '
                . 'contextid=' . $cContext->id . ' and roleid=5 and a.userid=u.id '
                . 'order by firstname desc';
        $rs = $DB->get_recordset_sql( $query );
        return $rs;
    }
    
    
    public static function league_get_student_name($userid){
        global $DB;
        $result = $DB->get_records_sql('SELECT * FROM {user} WHERE id = ?', array($userid));
        $alumno = "";
        foreach ($result as $rowclass)
        {
            $rowclass = json_decode(json_encode($rowclass), True);
            $alumno = $rowclass['firstname'] ." ".$rowclass['lastname'];
            return $alumno;
        }
        return null;
    }


    public static function getIDFileFromContenthash($contenthash){
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
    
    public static function get_notas_alumno($idleague, $cmid, $userid, $contextid){
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
    
    public static function getArrayMarkByStudent($idleague, $iduser, $toprint){
        global $DB;
        //Lista de estudiantes de un curso
        $var="select a.id, b.mark, a.published
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
    
    public static function publishedMarks($exercise){
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
    
    public static function get_notas_alumno_para_profesor($iduser, $idleague){
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

    
    public static function get_tabla_notas($idliga){
        $marks = array();
        $estudiantes = league_model::get_students();
        foreach($estudiantes as $e){
            $fila = new stdClass();
            $fila->id = $e->id;
            $fila->firstname = $e->firstname;
            $fila->lastname = $e->lastname;
            $fila->notas = league_model::get_notas_alumno_para_profesor($e->id, $idliga);
            array_push($marks, $fila);
        }
        return $marks;
    }

    
    public static function generateRandomFileID(){
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
    
    
    public static function getURLFile($contextid, $component, $filearea, $itemid, $name){
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


    public static function restoreURLFile($contextid, $itemid){
        $component = 'mod_league';
        $filearea = 'exuplod';
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
            foreach ($files as $file) {
                $contenthash = $file->get_contenthash();
                $id_file = \league_model::getIDFileFromContenthash($contenthash);


                $url = \league_model::getURLFile($file->get_contextid(), $file->get_component(), 
                        $file->get_filearea(), $file->get_itemid(), $file->get_filename());

                $resultado = new stdClass();
                $resultado->id = $id_file;
                $resultado->url = $url;
                return $resultado;
            }
        }
        return null;
    }
    
    
    public static function is_last_attempt($iduser, $idexer, $idattempt){
        global $DB;
        //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
        $var="select id
            from mdl_league_attempt
            where id_user = $iduser and exercise = $idexer
            order by id desc
            limit 1;";
        $data = $DB->get_records_sql($var);

        if($data){
            foreach ($data as $d){
                $d = get_object_vars($d);
                
                return $d['id'] == $idattempt;
            }
        }
        
        return false;
    }

    public static function getNameExerByID($id, $name = true){
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
    
    public static function isleagueexercise($idexer, $idleague){
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
        return false;
    }

    public static function getDataFromAttempt($id, $field){
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
}