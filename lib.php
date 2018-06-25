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
   
    
    print_r($league);
    
    $league->id = $DB->insert_record('league', $league);
   
    // You may have to add extra stuff in here.
    
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
    newmodule_grade_item_update($league);
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
