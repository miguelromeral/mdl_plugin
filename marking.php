<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');

defined('MOODLE_INTERNAL') || die();

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$exerciseid = required_param('exercise', PARAM_INT);
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);

$PAGE->set_url('/mod/league/marking.php', array('id' => $cm->id));

if ($cmid) {
    if (!$cm = get_coursemodule_from_id('league', $cmid)) {
        print_error('Course Module ID was incorrect'); // NOTE this is invalid use of print_error, must be a lang string id
    }
    if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
        print_error('course is misconfigured');  // NOTE As above
    }
    if (!$league = $DB->get_record('league', array('id'=> $cm->instance))) {
        print_error('course module is incorrect'); // NOTE As above
    }
    require_course_login($course, true, $cm);
} else {
    print_error('missingparameter');
}

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$PAGE->set_pagelayout('standard');

// Mark viewed if required
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Print header.
$PAGE->set_title(format_string(get_string('management_title', 'league')));
$PAGE->set_heading(format_string($course->fullname));

$modinfo = get_fast_modinfo($course);
$cm_info = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cm_info,  context_module::instance($cm->id));

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!has_capability('mod/league:view', $context)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}

/// find out current groups mode
//groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/league/marking.php?id=' . $cm->id);
//$currentgroup = groups_get_activity_group($cm);
//$groupmode = groups_get_activity_groupmode($cm);

$output = $PAGE->get_renderer('mod_league');

echo $output->header();


if($mod->usermarkstudents($USER->id) && ($exerciseid == -1 || isleagueexercise($exerciseid, $league->id))){
    $attempts = get_total_students_exercises($exerciseid);
    $panel = new total_attempts_view($cmid, $attempts, $exerciseid, getNameExerByID($exerciseid), $context->id);
    echo $output->render($panel);
}else{
    $panel = new go_back_view(
            get_string('notallowedpage','league'), 
            get_string('nopermission','league'), 
            $cmid,
            'view.php');
    echo $output->render($panel);
    
}
echo $output->footer();

