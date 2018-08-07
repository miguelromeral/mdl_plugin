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
 * Library of functions for the league module.
 *
 * This contains functions that are called also from outside the league module.
 *
 * @package     mod_league
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');
require_once($CFG->dirroot.'/mod/league/classes/league.php');

/**
 * @param string $feature FEATURE_xx constant for requested feature.
 * @return boolean True if league supports feature.
 */
function league_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        default: return null;
    }
}

/**
 * Saves a new instance of the league into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $league Submitted data from the form in mod_form.php
 * @param mod_league_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted league record
 */
function league_add_instance(stdClass $league, mod_league_mod_form $mform = null) {
    global $DB;
    $league->timemodified = time();
    $league->filearea = 'exuplod';
    $league->intro = 'exuplod';
    $league->id = $DB->insert_record('league', $league);
    // Create the Gradebook.
    league_grade_item_update($league);
    // Trigger the League Created.
    mod_league\league::trigger_league_created_event($league);
    
    return $league->id;
}

/**
 * Updates an instance of the league in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $league An object from the form in mod_form.php
 * @param mod_league_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function league_update_instance(stdClass $league, mod_league_mod_form $mform = null) {
    global $DB;
    $league->timemodified = time();
    $league->id = $league->instance;
    $result = $DB->update_record('league', $league);
    // Trigger update event.
    \mod_league\league::trigger_league_updated_event($league);
    // Update the Gradebook.
    league_grade_item_update($league);
    return $result;
}

/**
 * Removes an instance of the league from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the league instance.
 * @return boolean Success/Failure.
 */
function league_delete_instance($id) {
    global $DB;
    if (! $league = $DB->get_record('league', array('id' => $id))) {
        return false;
    }
    // Delete any dependent records here.
    $DB->delete_records('league', array('id' => $league->id));
    // Delete Gradebook.
    league_grade_item_delete($league);
    return true;
}

/**
 * Add a new exercise instance.
 * 
 * @global object $DB Moodle database.
 * @param string $name Exercise name.
 * @param string $statement Exercise statement.
 * @param object $leagueid League ID.
 * @return int New exercise ID.
 */
function league_exercise_add_instance($name, $statement, $leagueid) {
    global $DB;
    $record = new stdClass();
    $record->name = $name;
    $record->timemodified = time();
    $record->statement = $statement;
    $record->league = $leagueid;
    // Automatically disabled and unpublished.
    $record->enabled = 0;
    $record->published = 0;
    $id = $DB->insert_record('league_exercise', $record);
    return $id;
}

/**
 * Update an exercise instance.
 * 
 * @global object $DB Moodle database.
 * @param string $name Exercise name.
 * @param string $statement Exercise statement.
 * @param int $leagueid League ID.
 * @param int $exerciseid Exercise ID.
 * @param bool $enabled Exercise enabled.
 * @param bool $published Marks exercise published.
 * @return type
 */
function league_exercise_update_instance($name, $statement, $leagueid, $exerciseid, $enabled, $published) {
    global $DB;
    $record = new stdClass();
    $record->id = $exerciseid;
    $record->name = $name;
    $record->timemodified = time();
    $record->statement = $statement;
    $record->league = $leagueid;
    $record->enabled = $enabled;
    $record->published = $published;
    $id = $DB->update_record('league_exercise', $record);
    return $id;
}

/**
 * Delete an exercise instance.
 * 
 * @global object $DB Moodle database.
 * @param int $id Exercise ID.
 * @return boolean Success / Fail.
 */
function league_exercise_delete_instance($id) {
    global $DB;
    if (! $exercise = $DB->get_record('league_exercise', array('id' => $id))) {
        return false;
    }
    $DB->delete_records('league_exercise', array('id' => $exercise->id));
    league_attempt_delete_all_instances($id);
    return true;
}


/**
 * Add an attempt instance.
 * 
 * @global object $DB Moodle database.
 * @param int $userid User ID.
 * @param int $exerciseid Exercise ID.
 * @param int $itemid File Item ID.
 * @param string $filename File name (also the attempt name).
 * @param int $leagueid League ID.
 * @return int New attempt ID.
 */
function league_attempt_add_instance($userid, $exerciseid, $itemid, $filename, $leagueid) {
    global $DB;
    $record = new stdClass();
    $time = time();
    $record->name = ($filename ? $filename : "$userid-$exerciseid-$time");
    $record->timemodified = $time;
    $record->timecreated = $time;
    $record->user = $userid;
    $record->exercise = $exerciseid;
    $record->mark = -1;
    $record->itemid = $itemid;
    $record->league = $leagueid;
  
    $id = $DB->insert_record('league_attempt', $record);
   
    return $id;
}

/**
 * Update an attempt instance.
 * 
 * @global object $DB Moodle database.
 * @param object $league League instance.
 * @param int $attemptid Attempt ID.
 * @param int $mark Attempt mark.
 * @param string $observations Attempt observations.
 * @param int $exerciseid Exercise ID.
 * @return int Attempt ID.
 */
function league_attempt_update_instance($league, $attemptid, $mark, $observations, $exerciseid) {
    global $DB;
    $record = new stdClass();
    $record->id = $attemptid;
    $time = time();
    $record->timemodified = $time;
    $record->observations = $observations;
    $record->mark = $mark;
    
    $id = $DB->update_record('league_attempt', $record);
    // Update the grades only if the exercise has published marks.
    if(\league_model::has_exercise_published_marks($exerciseid)){
        league_update_grades($league);
    }
    
    return $id;
}

/**
 * Delete an attempt instance.
 * 
 * @global object $DB Moodle database.
 * @param int $id Attempt ID.
 * @return boolean Success / Fail.
 */
function league_attempt_delete_instance($id) {
    global $DB;
    if (! $exercise = $DB->get_record('league_attempt', array('id' => $id))) {
        return false;
    }
    $DB->delete_records('league_attempt', array('id' => $id));
    return true;
}

/**
 * Delete all attempts associated to an exercise.
 * 
 * @global object $DB Moodle database.
 * @param int $exerciseid Exercise ID.
 * @return boolean Success / Fail.
 */
function league_attempt_delete_all_instances($exerciseid){
    global $DB;
    if (! $exercise = $DB->get_record('league_attempt', array('exercise' => $exerciseid))) {
        return false;
    }
    $DB->delete_records('league_attempt', array('exercise' => $exerciseid));
    return true;
}

    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                       GRADEBOOK API                              //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////

/**
 * Update the Gradebook of the league.
 * 
 * @category grade
 * @global object $CFG Global Moodle configuration.
 * @param object $league League instance.
 * @param int $userid User ID for update specific user gradebook. 0 if update all users.
 * @param bool $nullifnone If a single user is specified, $nullifnone is true and the 
 * user has no grade then a grade item with a null rawgrade should be inserted.
 */
function league_update_grades($league, $userid=0, $nullifnone=true){
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    
    if ($grades = league_get_user_grades($league, $userid)) {
        // If the element is gradable, it tries to retrieve grades from users
        // with league_get_user_grades (that belongs to Rating API).
        league_grade_item_update($league, $grades);
    } else if ($userid and $nullifnone) {
        
        // If the user has no grades (or user = 0), then set NULL grade.
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        league_grade_item_update($league, $grade);
        
    } else {
        // In any case, update all grades.
        league_grade_item_update($league);
    }
}

/**
 * Create or upgrade a Grade Item for an league instance calling grade_update().
 * It can update the activity's grade and even the users grade (if we pass the
 * $grades value). Usually, $grades accepts the string 'reset' to reset the 
 * gradebook.
 * 
 * @category grade
 * @global object $CFG Global Moodle configuration.
 * @param object $league League instance.
 * @param object $grades Grades.
 * @return type
 */
function league_grade_item_update($league, $grades=NULL){
    global $CFG;
    //workaround for buggy PHP versions
    if (!function_exists('grade_update')) { 
        require_once($CFG->libdir.'/gradelib.php');
    }
 
    $params = array('itemname' => $league->name);
 
    if (isset($league->cmidnumber)) {
        // cmidnumber may not be always present.
        $params['idnumber'] = $league->cmidnumber;
    }
    
    $params['gradetype'] = GRADE_TYPE_VALUE;
    $params['grademax']  = 100;
    $params['grademin']  = 0;

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }
    
    return grade_update('mod/league', $league->course, 'mod', 'league', $league->id, 0, $grades, $params);
}

/**
 * Delete grade item for given league.
 * 
 * @category grade
 * @global object $CFG Global Moodle configuration.
 * @param object $league League instance.
 * @return object League.
 */
function league_grade_item_delete($league){
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    return grade_update('mod/league', $league->course, 'mod', 'league', $league->id, 0,
            null, array('deleted' => 1));
}

    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                          RATING API                              //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////


//Devuelve las notas delos usuarios. IMPLEMENTAR SI USERID = 0

/**
 * Return users grade (global).
 * 
 * @global object $DB Moodle database.
 * @param object $league League instance.
 * @param int $userid User ID.
 * @return object
 */
function league_get_user_grades($league, $userid = 0){
    global $DB;
    $grades = array();
    if($userid != 0){
        // A specific user
        $marks = \league_model::get_mark_array_by_student($league->id, $userid, false);
        // Number of exercises
        $count = sizeof($marks);
        // Sum of all attempt marks.
        $sum = array_sum($marks);
        // Grade is an average.
        $average = ($count ? (int) $sum / $count : 0);
        // Return the user grade.
        $grades[$userid] = (object)array('userid'=>$userid, 'rawgrade'=>$average);
    }else{
        // We get all students grades.
        $students = \league_model::get_students();
        foreach($students as $user){
            $uid = $user->id;
            // The same process than before.
            $marks = \league_model::get_mark_array_by_student($league->id, $uid, false);
            $count = sizeof($marks);
            $sum = array_sum($marks);
            $average = ($count ? (int) $sum / $count : 0);
            $grades[$uid] = (object)array('userid'=>$uid, 'rawgrade'=>$average);
        }
    }
    return $grades;
}

    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                            FILE API                              //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////

/**
 * Return the file if the user can download the file.
 * 
 * @param object $course Course.
 * @param object $cm Course Module.
 * @param object $context Current context.
 * @param string $filearea File Area.
 * @param object $args Arguments to download the file.
 * @param bool $forcedownload Force download to the explorer or download in the file.
 * @param array $options Options to download the file.
 */
function mod_league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options);
}

/**
 * Return the file if the user can download the file.
 * 
 * @param object $course Course.
 * @param object $cm Course Module.
 * @param object $context Current context.
 * @param string $filearea File Area.
 * @param object $args Arguments to download the file.
 * @param bool $forcedownload Force download to the explorer or download in the file.
 * @param array $options Options to download the file.
 * @return file Success / Fail.
 */
function league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is a context module (for the league).
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false; 
    }
    
    // Check the filearea is the league's file area (We can't download others
    // module files.
    if ($filearea !== 'exuplod') {
        return false;
    }
    
    // Make sure the user is logged in and has access to the module
    // (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true, $cm);
 
    // Check capability if the user can download files.
    if (!has_capability('mod/league:downloadfiles', $context)) {
        return false;
    }
    
    // Retrieve item ID and file name from aruments.
    $itemid = (int) array_shift($args);
    $filename = array_pop($args);
    if (!$args) {
        // $args is empty => the path is '/'
        $filepath = '/'; 
    } else {
        // $args contains elements of the filepath.
        $filepath = '/'.implode('/', $args).'/'; 
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_league', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        // The file does not exist.
        return false;
    }
    
    // Get appropiate data belonging to this attempt to be able to trigger the event.
    $ids = \league_model::get_attempt_data_by_itemid($itemid);
    if($ids){
        \mod_league\league::trigger_attempt_downloaded_event($ids->id, $ids->user, 
                $ids->league, $ids->exercise, $context);
    }
  
    // We can now send the file back to the browser.
    send_stored_file($file, 0, 0, true); // download MUST be forced - security!
}

    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                         NAVIGATION API                           //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////

/**
 * Extends navigation API to set new nodes.
 * 
 * @param navigation_node $leaguenode Node with League instance.
 * @param stdclass $course Course.
 * @param stdclass $module Module.
 * @param cm_info $cm Course module info.
 */
function league_extend_navigation(navigation_node $leaguenode, stdclass $course, stdclass $module, cm_info $cm) {
    // Add a new node with Qualy link.
    $qualynode = $leaguenode->add(get_string('qualy_title', 'league'),
            new moodle_url('/mod/league/qualy.php', array('id' => $leaguenode->key)));
    // Make the node active.
    $qualynode->make_active();
}

/**
 * Extends the settings navigation with the League settings.
 * 
 * @param settings_navigation $settingsnav Settings Site navigation node.
 * @param navigation_node $leaguenode League node.
 */
function league_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $leaguenode=null) {
    // No special nodes for admin settings.
}