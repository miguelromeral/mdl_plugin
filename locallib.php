<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/league/lib.php');

function league_created_handler($event) {
    global $DB;
    $course  = $DB->get_record('course', array('id' => $event->courseid));
    $cm      = get_coursemodule_from_id('quiz', $event->get_context()->instanceid, $event->courseid);
    if (!($course && $cm)) {
        // Something has been deleted since the event was raised. Therefore, the
        // event is no longer relevant.
        return true;
    }
    // Update completion state.
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm)) {
        $completion->update_state($cm, COMPLETION_COMPLETE, $event->userid);
    }
    
    //return quiz_send_notification_messages($course, $quiz, $attempt,
    //        context_module::instance($cm->id), $cm);
    return true;      
}

function league_updated_handler($event) {
    return true;      
}

function exercise_created_handler($event) {
    return true;      
}

function exercise_updated_handler($event) {
    return true;      
}

function exercise_deleted_handler($event) {
    return true;      
}

function attempt_submitted_handler($event) {
    return true;      
}

function attempt_downloaded_handler($event) {
    return true;      
}

function attempt_graded_handler($event) {
    return true;      
}

function league_created($league) {
    
    $params = array(
        'objectid' => $league->id,
        'context' => context_module::instance($league->coursemodule)
    );
    
    $event = \mod_league\event\league_created::create($params);
    $event->trigger();
}

function league_exercise_created($league, $id, $context) {
    
    $params = array(
        'objectid' => $id,
        'other' => array('league' => $league),
        'context' => $context
    );
    
    $event = \mod_league\event\exercise_created::create($params);
    $event->trigger();
}

function league_attempt_submitted($exercise, $id, $context) {
    
    $params = array(
        'objectid' => $id,
        'other' => array('exercise' => $exercise),
        'context' => $context
    );
    
    $event = \mod_league\event\attempt_submitted::create($params);
    $event->trigger();
}

function league_attempt_graded($attemptid, $user, $exercise, $mark, $context) {
    
    $params = array(
        'objectid' => $attemptid,
        'relateduserid' => $user,
        'other' => array('exercise' => $exercise,
                        'mark' => $mark),
        'context' => $context
    );
    
    $event = \mod_league\event\attempt_graded::create($params);
    $event->trigger();
}