<?php

//namespace mod_league\model;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

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

}