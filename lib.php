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

function attempt_add_instance($course, $id_user, $exercise, $id_file, $url, $name = null) {
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
    $record->mark = -1;
    $record->id_file = $id_file;
    $record->url = $url;
    
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

function attempt_update_instance($league, $idat, /*$course, $id_user, $exercise, $task, */ $mark) {
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
    league_update_grades($league);
    
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


function league_update_grades($league, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
 
    if (! isset($league->id)) {
        return false;
    }

    $grades = league_get_grades($league, $userid);

    if (count($grades)) {
        return league_grade_item_update($league, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        return league_grade_item_update($league, $grade);
 
    } else {
        return league_grade_item_update($league);
    }
}

require_once($CFG->dirroot.'/rating/lib.php');

function league_get_user_grades($league, $userid){
    $ratingoptions = new stdClass;
    $ratingoptions->component = 'mod_league';
    $ratingoptions->ratingarea = 'post';
    $ratingoptions->modulename = 'league';
    $ratingoptions->moduleid   = $league->id;
    $ratingoptions->userid = $userid;
    //$ratingoptions->aggregationmethod = $league->assessed;
    //$ratingoptions->scaleid = $league->scale;
    //$ratingoptions->itemtable = 'forum_posts';
    $ratingoptions->itemtableusercolumn = 'userid';

    $rm = new rating_manager();
    return $rm->get_user_grades($ratingoptions);
}

function league_get_grades($league, $userid = null) {
    global $DB;

    $where = (isset($userid) ? "where id_user = ".$userid : "");
    
    $sql = "select * from mdl_attempt $where";

    $grades = array();
    if ($aggregates = $DB->get_records_sql($sql)) {
        foreach ($aggregates as $userid => $aggregate) {
            $grades[$userid] = (object)array('userid'=>$userid, 'rawgrade'=>$aggregate->mark, 'maxstatus' => 100);
        }
    }
    return $grades;
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                              //
////////////////////////////////////////////////////////////////////////////////

function mod_league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options);
}

function league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false; 
    }
 
  /*  if ($filearea !== 'expectedfilearea' && $filearea !== 'anotherexpectedfilearea') {
        return false;
    }
 */
    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    //require_login($course, true, $cm);
    require_login();
 
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/league:view', $context)) {
        return false;
    }
 
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = (int) array_shift($args);
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_league', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }
    
    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering. 
    // From Moodle 2.3, use send_stored_file instead.
    send_stored_file($file, 0, 0, true, $filename); // download MUST be forced - security!
}