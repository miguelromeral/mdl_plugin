<?php

defined('MOODLE_INTERNAL') || die();
/**
 * Saves a new instance of the newmodule into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $league Submitted data from the form in mod_form.php
 * @param mod_newmodule_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted newmodule record
 */
function league_add_instance(stdClass $league, mod_league_mod_form $mform = null) {
    global $DB;
    $league->timemodified = time();
    $league->gradeweighting = 100;
    
    //print_r($league);
    
    $league->id = $DB->insert_record('league', $league);
    //Creamos Gradebook
    league_grade_item_update($league);
    
    
    return $league->id;
}

/**
 * Updates an instance of the newmodule in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $league An object from the form in mod_form.php
 * @param mod_newmodule_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function league_update_instance(stdClass $league, mod_league_mod_form $mform = null) {
    global $DB;
    $league->timemodified = time();
    $league->id = $league->instance;
    // You may have to add extra stuff in here.
    $result = $DB->update_record('league', $league);
    league_grade_item_update($league);
    return $result;
}

/**
 * Removes an instance of the newmodule from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function league_delete_instance($id) {
    global $DB;
    if (! $league = $DB->get_record('league', array('id' => $id))) {
        return false;
    }
    // Delete any dependent records here.
    $DB->delete_records('league', array('id' => $league->id));
    //newmodule_grade_item_delete($league);
    return true;
}


function exercise_add_instance($course, $name, $statement, $league) {
    global $DB;
    $record = new stdClass();
    $record->course = $course;
    $record->name = $name;
    $record->timemodified = time();
    $record->statement = $statement;
    $record->intro = null;
    $record->introformat = null;
    $record->league = $league;
    $record->enabled = 0;
  
    $id = $DB->insert_record('exercise', $record);
   
    if($id){
        return true;
    }else{
        return false;
    }
}

function exercise_update_instance($course, $name, $statement, $league, $idexer, $enabled) {
    global $DB;
    $record = new stdClass();
    $record->id = $idexer;
    $record->course = $course;
    $record->name = $name;
    $record->timemodified = time();
    $record->statement = $statement;
    $record->intro = null;
    $record->introformat = null;
    $record->league = $league;
    $record->enabled = $enabled;
    $id = $DB->update_record('exercise', $record);
    
    if($id){
        return true;
    }else{
        return false;
    }
}

function exercise_delete_instance($id) {
    global $DB;
    if (! $exercise = $DB->get_record('exercise', array('id' => $id))) {
        return false;
    }
    // Delete any dependent records here.
    $DB->delete_records('exercise', array('id' => $exercise->id));
    //newmodule_grade_item_delete($league);
    return true;
}

function attempt_add_instance($course, $id_user, $exercise, $task, $name = null) {
    global $DB;
    $record = new stdClass();
    $record->course = $course;
    $time = time();
    $record->name = ($name ? $name : "$id_user-$exercise-$time");
    $record->timemodified = $time;
    $record->id_user = $id_user;
    $record->intro = null;
    $record->introformat = null;
    $record->exercise = $exercise;
    $record->task = $task;
    $record->mark = -1;
    
    //echo "Voy a añadir:<br>";
    //print_r($record);
    //echo "<br>----<br>";
  
    $id = $DB->insert_record('attempt', $record);
   
    if($id){
        return true;
    }else{
        return false;
    }
}

function attempt_update_instance($idat, /*$course, $id_user, $exercise, $task, */ $mark) {
    global $DB;
    $record = new stdClass();
    $record->id = $idat;
    //$record->course = $course;
    $time = time();
    //$record->name = "$id_user-$exercise-$time";
    $record->timemodified = $time;
    //$record->id_user = $id_user;
    //$record->intro = null;
    //$record->introformat = null;
    //$record->exercise = $exercise;
    //$record->task = $task;
    $record->mark = $mark;
    
    //echo "Voy a añadir:<br>";
    //print_r($record);
    //echo "<br>----<br>";
  
    $id = $DB->update_record('attempt', $record);
   
    if($id){
        return true;
    }else{
        return false;
    }
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////


function league_grade_item_update($league, $grades=null) {
    global $CFG;
    require_once($CFG->dirroot.'/lib/gradelib.php');
    
    // sanity check on $hotpot->id
    if (empty($league->id) || empty($league->course)) {
        return;
    }
    
    // set up params for grade_update()
    $params = array(
        'itemname' => $league->name
    );
    if ($grades==='reset') {
        $params['reset'] = true;
        $grades = null;
    }
    if (isset($league->cmidnumber)) {
        //cmidnumber may not be always present
        $params['idnumber'] = $league->cmidnumber;
    }
    if ($league->gradeweighting) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $league->gradeweighting;
        $params['grademin']  = 0;
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
        // Note: when adding a new activity, a gradeitem will *not*
        // be created in the grade book if gradetype==GRADE_TYPE_NONE
        // A gradeitem will be created later if gradetype changes to GRADE_TYPE_VALUE
        // However, the gradeitem will *not* be deleted if the activity's
        // gradetype changes back from GRADE_TYPE_VALUE to GRADE_TYPE_NONE
        // Therefore, we force the removal of empty gradeitems
        $params['deleted'] = true;
    }
    return grade_update('mod/league', $league->course, 'mod', 'league', $league->id, 0, $grades, $params);
}


function league_update_grades($league=null, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    // get hotpot object
    //require_once($CFG->dirroot.'/mod/league/locallib.php');
    if ($league===null) {
        /*
        // update/create grades for all hotpots
        // set up sql strings
        $strupdating = get_string('updatinggrades', 'mod_hotpot');
        $select = 'h.*, cm.idnumber AS cmidnumber';
        $from   = '{hotpot} h, {course_modules} cm, {modules} m';
        $where  = 'h.id = cm.instance AND cm.module = m.id AND m.name = ?';
        $params = array('hotpot');
        // get previous record index (if any)
        $configname = 'update_grades';
        $configvalue = get_config('mod_hotpot', $configname);
        if (is_numeric($configvalue)) {
            $i_min = intval($configvalue);
        } else {
            $i_min = 0;
        }
        if ($i_max = $DB->count_records_sql("SELECT COUNT('x') FROM $from WHERE $where", $params)) {
            if ($rs = $DB->get_recordset_sql("SELECT $select FROM $from WHERE $where", $params)) {
                if (defined('CLI_SCRIPT') && CLI_SCRIPT) {
                    $bar = false;
                } else {
                    $bar = new progress_bar('hotpotupgradegrades', 500, true);
                }
                $i = 0;
                foreach ($rs as $hotpot) {
                    // update grade
                    if ($i >= $i_min) {
                        upgrade_set_timeout(); // apply for more time (3 mins)
                        hotpot_update_grades($hotpot, $userid, $nullifnone);
                    }
                    // update progress bar
                    $i++;
                    if ($bar) {
                        $bar->update($i, $i_max, $strupdating.": ($i/$i_max)");
                    }
                    // update record index
                    if ($i > $i_min) {
                        set_config($configname, $i, 'mod_hotpot');
                    }
                }
                $rs->close();
            }
        }
        // delete the record index
        unset_config($configname, 'league');
        return; // finish here
        */
        return;
    }
    // sanity check on $hotpot->id
    if (! isset($league->id)) {
        return false;
    }
    $grades = league_get_grades($league, $userid);
    if (count($grades)) {
        league_grade_item_update($league, $grades);
    } else if ($userid && $nullifnone) {
        // no grades for this user, but we must force the creation of a "null" grade record
        league_grade_item_update($league, (object)array('userid'=>$userid, 'rawgrade'=>null));
    } else {
        // no grades and no userid
        league_grade_item_update($league);
    }
}