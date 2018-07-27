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
}