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
    
}