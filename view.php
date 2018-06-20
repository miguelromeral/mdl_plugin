<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);    // Course Module ID

$params = array();
if ($id) {
    $params['id'] = $id;
}
$PAGE->set_url('/mod/k/view.php', $params);


if ($id) {
    if (!$cm = get_coursemodule_from_id('league', $id)) {
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



// Mark viewed if required
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Print header.

$PAGE->set_title(format_string($league->name));
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
groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/league/view.php?id=' . $cm->id);
$currentgroup = groups_get_activity_group($cm);
$groupmode = groups_get_activity_groupmode($cm);

$bc = new block_contents();

$var="SELECT * 
FROM mdl_role as er
INNER JOIN mdl_role_assignments as era 
ON era.roleid=er.id
WHERE userid = $USER->id";
$data = $DB->get_records_sql($var);
$rol = null;
foreach ($data as $rowclass)
{
    $rowclass = json_decode(json_encode($rowclass), True);
    switch ($rowclass['shortname']){
        case 'student':
            $rol = 'student';
            break;
        case 'teacher' || 'editingteacher':
            $rol = 'teacher';
            break;
    }
}

if ($rol == 'student'){
    ?>
<h1>Actividades disponibles</h1>
    <?php
}else if($rol == 'teacher'){
    echo "Página principal del profesor.";
}else{
    notice(get_string('noviewdiscussionspermission', 'league'));
}
?>