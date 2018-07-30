<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the Model class (MVC).
 * 
 * This class is used to get records from database. On this way, we keep
 * the MVC design pattern.
 *
 * @package     mod_league
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * A full model on League.
 * 
 * This class is used as a Model (Like an MVC pattern). It can retrieve 
 * database records.
 * 
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class league_model {
    
    /**
     * Retrieve an user record given its ID.
     * 
     * @global object $DB Moodle database.
     * @param int $userid User ID.
     * @return object User info.
     */
    public static function get_user_by_id($userid){
        global $DB;
        return $DB->get_record('user', array('id' => $userid));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Return the user picture given its ID.
     * 
     * @global object $COURSE Current course.
     * @global object $DB Moodle database.
     * @global object $OUTPUT Moodle core output.
     * @param int $userid User ID.
     * @return string HTML string with the user picture to be printed.
     */
    public static function get_user_image($userid){
        global $COURSE, $DB, $OUTPUT;
        $cContext = context_course::instance($COURSE->id);
        $query = 'select u.id as id, firstname, lastname, picture, imagealt, '
                . 'email, u.* from mdl_role_assignments as a, mdl_user as u where '
                . 'contextid=' . $cContext->id . ' and roleid=5 and a.userid=u.id';
        $rs = $DB->get_recordset_sql( $query );
        foreach( $rs as $r ) {
            if($r->id == $userid){
                return $OUTPUT->user_picture($r, array('courseid'=>$COURSE->id));
            }
        }
        return null;
    }
    
    /**
     * Return all exercises given the league id.
     * 
     * @global object $DB Moodle database.
     * @param int $leagueid
     * @return object 
     */
    public static function get_exercises_from_id($leagueid){
        global $DB;
        $query = "SELECT * 
                    FROM {league_exercise}
                   WHERE league = :id
                ORDER BY id";
        return $DB->get_records_sql($query, array('id' => $leagueid));
    }
    
    /**
     * Return a league instance given the course ID.
     * 
     * @global object $DB Moodle database.
     * @param int $courseid
     * @return object
     */
    public static function get_league_from_course($courseid){
        global $DB;
        $query = "SELECT * 
                    FROM {league}
                   WHERE course = :id";
        
        return $DB->get_records_sql($query, array('id' => $courseid));
    }
    
    /**
     * Return all exercises to an user (it also counts the number of attempts).
     * 
     * @global object $DB Moodle database.
     * @param int $leagueid League ID.
     * @param int $userid User ID.
     * @return object
     */
    public static function get_exercises_from_id_by_user($leagueid, $userid){
        global $DB;
        $query = "SELECT e.*, a.num 
                    FROM {league_exercise} AS e
               LEFT JOIN (
                            SELECT COUNT(id) AS num, exercise
                              FROM {league_attempt}
                             WHERE id_user = :user
                          GROUP BY exercise
                         ) 
                         AS a ON e.id = a.exercise
                   WHERE league = :league
                ORDER BY id";

        return $DB->get_records_sql($query, array('league' => $leagueid, 'user' => $userid));
    }
    
    /**
     * Return all attempts (sorted by time of modification) given an exercise ID.
     * 
     * @global object $DB Moodle database.
     * @param int $exerciseid Exercise ID.
     * @return object
     */
    public static function get_attempts_by_exercise($exerciseid){
        global $DB;
        $query = "SELECT *
                    FROM {league_attempt} AS a
                    JOIN (
                            SELECT id AS us, firstname, lastname
                              FROM {user}
                         ) 
                         AS b ON a.id_user = b.us
                   WHERE a.exercise = :id
                ORDER BY a.timemodified DESC";
        
        return $DB->get_records_sql($query, array('id' => $exerciseid));
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
    
    /**
     * Return student name
     * 
     * @global object $DB Moodle database.
     * @param int $userid User ID.
     * @return string
     */
    public static function get_student_name($userid){
        global $DB;
        $query = "SELECT *
                    FROM {user}
                    WHERE id = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $userid));
        
        $name = "";
        foreach ($data as $row)
        {
            $name = $row->firstname . ' ' . $row->lastname;
        }
        return $name;
    }

    /**
     * Return File ID given its content hash.
     * 
     * @global object $DB Moodle database.
     * @param string $contenthash Content hash.
     * @return int File ID
     */
    public static function get_file_id_from_content_hash($contenthash){
        global $DB;
        $query = "SELECT MAX(id) AS m
                    FROM {files}
                   WHERE contenthash = :ch";

        $data = $DB->get_records_sql($query, array('ch' => $contenthash));
        
        $id = -1;
        foreach ($data as $d){
            foreach($d as $i => $l){
                $id = $l;
            }
        }
        return $id;
    }
    
    /**
     * Return all student marks to a league.
     * 
     * @global object $DB Moodle database.
     * @param int $leagueid League ID.
     * @param int $userid User ID.
     * @return object
     */
    public static function get_student_marks($leagueid, $userid){
        global $DB;
        $query = "SELECT *
                    FROM {league_exercise} AS a
         LEFT OUTER JOIN (
                            SELECT a.id AS idat, a.timemodified AS tma,
                                   a.observations, a.name as fname,
                                   a.exercise, b.id_user, a.mark, a.id_file
                              FROM mdl_league_attempt AS a
                        INNER JOIN (
                                        SELECT MAX(id) AS m, id_user
                                          FROM {league_attempt}
                                         WHERE id_user = :user
                                      GROUP BY exercise
                                    ) 
                                    AS b ON a.id = b.m
                         ) 
                         AS b ON a.id = b.exercise
                   WHERE a.league = :league";
        
        return $DB->get_records_sql($query, array('user' => $userid, 'league' => $leagueid));
    }
    
    /**
     * Return an array with the best marks of a student sorted from highest to smallest.
     * 
     * @global object $DB Moodle database.
     * @param int $leagueid League ID.
     * @param int $userid User ID.
     * @param bool $toprint Handles -1 mark (do not marked yet) to TBA.
     * @return array
     */
    public static function get_mark_array_by_student($leagueid, $userid, $toprint){
        global $DB;
        $query = "SELECT a.id, b.mark, a.published
                    FROM {league_exercise} AS a
         LEFT OUTER JOIN (
                            SELECT a.id AS idat, a.timemodified AS tma,
                                   a.observations, a.name AS fname,
                                   a.exercise, b.id_user, a.mark, a.id_file
                              FROM {league_attempt} AS a
                        INNER JOIN (
                                            SELECT MAX(id) AS m, id_user
                                              FROM {league_attempt}
                                             WHERE id_user = :user
                                          GROUP BY exercise
                                    ) 
                                    AS b ON a.id = b.m
                        )
                        AS b ON a.id = b.exercise
                WHERE a.league = :league
             ORDER BY mark DESC";
        
        $data = $DB->get_records_sql($query, array('user' => $userid, 'league' => $leagueid));
        $mark = Array();
        foreach ($data as $d){
            // If the attempt is not marked yet
            if ($d->mark != -1){
                // If is the exercise is to print or the exercise is published, add it.
                if($toprint || $d->published == 1){
                    array_push($mark, $d->mark);
                }
            }else{
                // If not, and is only to print, add it.
                if($toprint){
                    array_push($mark, get_string('q_tba','league'));
                }
            }
        }
        
        return $mark;
    }
    
    /**
     * Return the context ID from a league instance.
     * 
     * @global object $DB Moodle database.
     * @param int $leagueid League ID.
     * @return int
     */
    public static function get_context_module_id_from_league($leagueid){
        global $DB;
        $query = "SELECT l.id, m.id AS cm
                    FROM {league} AS l
              INNER JOIN (
                                SELECT cm.id, cm.module, cm.instance
                                  FROM {course_modules} AS cm
                            INNER JOIN {modules} AS m ON m.id = cm.module
                                 WHERE m.name = 'league'
                         ) 
                         AS m ON l.id = m.instance
                   WHERE l.id = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $leagueid));
        foreach ($data as $d){
            return $d->cm;
        }
        return null;
    }
    
    /**
     * 
     * @global object $DB
     * @param type $exercise
     * @return boolean
     */
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
                $id_file = \league_model::get_file_id_from_content_hash($contenthash);


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