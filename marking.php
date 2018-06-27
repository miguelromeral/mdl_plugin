<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$id_exer = required_param('id_exer', PARAM_INT);
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$info = get_fast_modinfo($course);
$name_exer = required_param('name', PARAM_TEXT);
//print_object($info);

/*
 * La variable $PAGE configura la página
 * La variable $OUTPUT muestra la página
 */

require_login($course, true, $cm);
/*
 * ABSOLUTAMENTE NECESARIO PONER EL URL.
 * Por lo menos, el id, después se pueden poner otras 'key' => 'value'
 * Convierte todo lo que le pasamos a un objeto moodle_url
 */
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

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);

//Pone como diseño el estandar de Moodle
$PAGE->set_pagelayout('standard');

// Mark viewed if required
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Print header.
$PAGE->set_title(format_string(get_string('management_title', 'league')));
//$PAGE->add_body_class('forumtype-'.$league->type);
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!has_capability('mod/league:view', $context)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}

/// find out current groups mode
groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/league/marking.php?id=' . $cm->id);
$currentgroup = groups_get_activity_group($cm);
$groupmode = groups_get_activity_groupmode($cm);

$bc = new block_contents();

// Recuperamos el ID del profesor y del modulo, si no coinciden, se mostrará un aviso para que salga.
$var="SELECT c.id as course, c.shortname, u.id as teacher, u.username, u.firstname || ' ' || u.lastname AS name FROM mdl_course c LEFT OUTER JOIN mdl_context cx ON c.id = cx.instanceid LEFT OUTER JOIN mdl_role_assignments ra ON cx.id = ra.contextid AND ra.roleid = '3' LEFT OUTER JOIN mdl_user u ON ra.userid = u.id WHERE cx.contextlevel = '50' AND c.id = $cm->course AND u.id = $USER->id";
$valido = $DB->get_records_sql($var);

if($valido == 0){
    ?>
        Por desgracia, no pertenece a este curso
    <?php
}else{
    
    if($id_exer){
        print_students_exercise($cmid, $id_exer, $name_exer);
    }
    ?>
        
<?php
echo $OUTPUT->footer();

}
