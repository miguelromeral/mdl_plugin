<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);

$PAGE->set_url('/mod/league/qualy.php', array('id' => $cm->id));

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
$PAGE->set_title(format_string($league->name." - ".get_string('qualy_title', 'league')));
$PAGE->set_heading(format_string($course->fullname));

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!has_capability('mod/league:view', $context, $USER->id)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}

/// find out current groups mode
groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/league/view.php?id=' . $cm->id);
$currentgroup = groups_get_activity_group($cm);
$groupmode = groups_get_activity_groupmode($cm);
    

$modinfo = get_fast_modinfo($course);
$cm_info = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cm_info,  context_module::instance($cm->id));

$rol = null;

if($mod->userseequaly($USER->id)){
    $rol = 'teacher';
}else if($mod->userseerestrictedqualy($USER->id)){
    $rol = 'student';
}

if($rol == 'teacher'){
    $buttonqualy = '<form action="grade.php" method="get">
                    <input type="hidden" name="id" value="'. $cmid .'" />
                    <input type="submit" value="'. get_string('view_individual_marks', 'league') .'"/>
                </form>';
    $PAGE->set_button($buttonqualy);
}

$output = $PAGE->get_renderer('mod_league');

echo $output->header();

$q = get_qualy_array($league->id, $course->id, $rol, $league->method);

if ($rol == 'student' || $rol == 'teacher'){
        
    
    $panel = null;
    if($rol == 'student'){
        $panel = new qualy_view($cmid, $q, $USER->id, $rol);
    }else if($rol == 'teacher'){
        
        
        $qs = get_qualy_array($league->id, $course->id, 'student', $league->method);
        $panel = new qualy_view($cmid, $q, $USER->id, $rol, $qs);
    }
    if($panel) { 
        echo $output->render($panel);
    }
        
}else{
    $panel = new go_back_view(
            get_string('notallowedpage','league'), 
            get_string('nopermission','league'), 
            $cmid,
            'view.php');
    echo $output->render($panel);
}

echo $output->footer();

?>