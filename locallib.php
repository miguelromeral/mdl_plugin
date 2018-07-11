<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/league/lib.php');


function league_created_handler($event) {
    global $DB;

    $course  = $DB->get_record('course', array('id' => $event->courseid));
    $cm      = get_coursemodule_from_id('league', $event->get_context()->instanceid, $event->courseid);

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

function attempt_submitted_handler($event) {
    global $DB;

    $course  = $DB->get_record('course', array('id' => $event->courseid));
    $cm      = get_coursemodule_from_id('league', $event->get_context()->instanceid, $event->courseid);

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