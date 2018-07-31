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
                             WHERE user = :user
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
                         AS b ON a.user = b.us
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
                                   a.timecreated AS tmc,
                                   a.observations, a.name as fname,
                                   a.exercise, b.user, a.mark, a.itemid
                              FROM mdl_league_attempt AS a
                        INNER JOIN (
                                        SELECT MAX(id) AS m, user
                                          FROM {league_attempt}
                                         WHERE user = :user
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
                                   a.exercise, b.user, a.mark, a.itemid
                              FROM {league_attempt} AS a
                        INNER JOIN (
                                            SELECT MAX(id) AS m, user
                                              FROM {league_attempt}
                                             WHERE user = :user
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
     * Return if an exercise has published marks.
     * 
     * @global object $DB Moodle database.
     * @param int $exerciseid Exercise ID.
     * @return boolean
     */
    public static function has_exercise_published_marks($exerciseid){
        global $DB;
        $query = "SELECT a.published
                    FROM {league_exercise} AS a
                   WHERE id = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $exerciseid));
        foreach ($data as $exercise){
            if ($exercise->published == 0){
                return false;
            }else{
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get all individual marks for each exercise in league (only for the 
     * last attempts).
     * 
     * @global object $DB Moodle database.
     * @param int $userid User ID.
     * @param int $leagueid League ID.
     * @return array
     */
    public static function get_student_individual_marks_to_teacher($userid, $leagueid){
        global $DB;
        $query = "SELECT *
                    FROM {league_exercise} AS a
         LEFT OUTER JOIN (
                            SELECT a.id AS idat, a.timemodified AS tma,
                                   a.observations, a.name AS fname,
                                   a.exercise, b.user, a.mark, a.itemid
                              FROM {league_attempt} AS a
                        INNER JOIN (
                                        SELECT MAX(id) AS m, user
                                          FROM {league_attempt}
                                         WHERE user = :user
                                      GROUP BY exercise
                                   ) 
                                   AS b ON a.id = b.m
                         ) 
                         AS b ON a.id = b.exercise
                   WHERE a.league = :league";
        
        $data = $DB->get_records_sql($query, array('league' => $leagueid, 'user' => $userid));

        $total = array();
        foreach ($data as $exercise){
            $notas = new stdClass();
            $notas->exercise = $exercise->exercise;
            $notas->mark = $exercise->mark;
            array_push($total, $notas);
        }
        return $total;
    }

    /**
     * Return all individual marks for each student in the league.
     * 
     * @param int $leagueid League id
     * @return object Array collection with arrays with user individual marks.
     */
    public static function get_tabla_notas($leagueid){
        $marks = array();
        $students = league_model::get_students();
        
        foreach($students as $student){
            $fila = new stdClass();
            $fila->id = $student->id;
            $fila->firstname = $student->firstname;
            $fila->lastname = $student->lastname;
            $fila->notas = league_model::get_student_individual_marks_to_teacher($student->id, $leagueid);
            array_push($marks, $fila);
        }
        
        return $marks;
    }

    /**
     * Generate a random File ID checking that is the only one in the database.
     * 
     * @global object $DB Moodle database.
     * @return int File ID.
     */
    public static function generate_random_file_id(){
        global $DB;
        $min = 1;
        $max = 10000000000;
        $found = false;
        $random = -1;
        while(! $found){
            $found = true;
            $random = rand($min, $max);
            
            $query = "SELECT DISTINCT itemid
                                 FROM {league_attempt}";
            
            $data = $DB->get_records_sql($query);
            // Check if there is a file with this ID.
            // If not, repeat a random.
            foreach ($data as $file){
                if($file->itemid == $random){
                    $found = false;
                }
            } 
            
        }
        return $random;
    }
    
    /**
     * Create an URL with the current server setup.
     * 
     * @global object $CFG Global Moodle configuration.
     * @param int $contextid Context ID.
     * @param int $itemid File ID.
     * @param string $name File name.
     * @return string
     */
    public static function get_url_file($contextid, $itemid, $name){
        global $CFG;

        $url = $CFG->wwwroot;
        $url .= "/pluginfile.php/";
        $url .= ($contextid)."/";
        $url .= "mod_league/";
        $url .= "exuplod/";
        $url .= ($itemid)."/";
        $url .= $name;
        
        return $url;
    }

    /**
     * Restore a URL used to download the file with the item ID given.
     * 
     * @param int $contextid Context ID.
     * @param int $itemid item ID.
     * @return \stdClass File ID and URL to that file.
     */
    public static function restoreURLFile($contextid, $itemid){
        $component = 'mod_league';
        $filearea = 'exuplod';
        // Get all files.
        $fs = get_file_storage();
        
        // Retrieve all files for this module (it has have this Item ID).
        if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
            foreach ($files as $file) {
                // Content hash of the File.
                $contenthash = $file->get_contenthash();
                // Get the File ID from its content hash.
                $itemid = \league_model::get_file_id_from_content_hash($contenthash);
                // Restore URL.
                $url = \league_model::get_url_file($file->get_contextid(), $file->get_itemid(), $file->get_filename());
                // Return the result.
                $resultado = new stdClass();
                $resultado->id = $itemid;
                $resultado->url = $url;
                return $resultado;
            }
        }
        
        return null;
    }
    
    /**
     * Return if an attempt is the last one for the user and exercise.
     * 
     * @global object $DB Moodle database.
     * @param int $userid User ID.
     * @param int $exerciseid Exercise ID.
     * @param int $attemptid Attempt ID.
     * @return boolean True if is the last one.
     */
    public static function is_last_attempt($userid, $exerciseid, $attemptid){
        global $DB;
        $query = "SELECT id
                    FROM {league_attempt}
                   WHERE user = :user AND exercise = :exercise
                ORDER BY id DESC
                   LIMIT 1;";
        
        if($data = $DB->get_records_sql($query, array('user' => $userid, 'exercise' => $exerciseid))){
            foreach ($data as $attempt){
                return $attempt->id == $attemptid;
            }
        }
        
        return false;
    }

    /**
     * Retrieve specific data from an exercise.
     * 
     * @global object $DB Moodle database.
     * @param int $id Exercise ID.
     * @param string $field Exercise field to retrieve.
     * @return object
     */
    public static function get_data_from_exercise($id, $field){
        global $DB;
        $query = "SELECT *
                    FROM {league_exercise}
                   WHERE id = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $id));
        
        foreach ($data as $d){
            if(isset($d->$field)){
                return $d->$field;
            }
        }
        
        return null;
    }
    
    /**
     * Return if the exercise ID belongs to a league.
     * 
     * @global object $DB Moodle database.
     * @param int $exerciseid Exercise ID.
     * @param int $leagueid League ID.
     * @return boolean
     */
    public static function is_league_exercise($exerciseid, $leagueid){
        global $DB;
        $query = "SELECT id, league
                    FROM {league_exercise}
                   WHERE id = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $exerciseid));
        
        foreach ($data as $exercise){
            if($exercise->id){
                return ($exercise->league == $leagueid);
            }
        }
        
        return false;
    }

    /**
     * Retrieve specific data from an attempt.
     * 
     * @global object $DB Moodle database.
     * @param int $id Attempt ID.
     * @param string $field Field to retrieve.
     * @return object
     */
    public static function get_data_from_attempt($id, $field){
        global $DB;
        $query = "SELECT *
                    FROM {league_attempt}
                   WHERE id = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $id));
        
        foreach ($data as $attempt){
            if(isset($attempt->$field)){
                return $attempt->$field;
            }
        }
        
        return null;
    }

    /**
     * Return IDs according the Item ID.
     * 
     * @global object $DB Moodle database.
     * @param int Item ID.
     * @return object Object with IDs.
     */
    public static function get_attempt_data_by_itemid($itemid){
        global $DB;
        $result = new stdClass();
        $query = "SELECT id, user, league, exercise
                    FROM {league_attempt}
                   WHERE itemid = :id";
        
        $data = $DB->get_records_sql($query, array('id' => $itemid));
        
        foreach ($data as $attempt){
            $result->id = $attempt->id;
            $result->user = $attempt->user;
            $result->exercise = $attempt->exercise;
            $result->league = $attempt->league;
            return $result;
        }
        return $result;
    }
    
}