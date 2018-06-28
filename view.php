<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$info = get_fast_modinfo($course);
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
$PAGE->set_url('/mod/league/view.php', array('id' => $cm->id));

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


/*
 * ABSOLUTAMENTE NECESARIO. Podriamos poner:
        $PAGE->set_context(context_system::instance());
        $PAGE->set_context(context_coursecat::instance($categoryid));
        $PAGE->set_context(context_course::instance($courseid));
        $PAGE->set_context(context_module::instance($moduleid));
 * dependiendo de nuestras necesidades
 */
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);

//Pone como diseño el estandar de Moodle
$PAGE->set_pagelayout('standard');

// Mark viewed if required
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Print header.
$PAGE->set_title(format_string($league->name));
//$PAGE->add_body_class('forumtype-'.$league->type);
$PAGE->set_heading(format_string($course->fullname));

$output = $PAGE->get_renderer('mod_league');

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
    
    //echo $output->inicio_estudiante();
    
    print_exercises($league->id, 'student', $cmid);
    print_notas_alumno($league->id, $cmid, $USER->id);
    
    ?>
    
    
    <form action="qualy.php" method="get">
        <input type="hidden" name="id" value="<?= $cmid ?>" />
        <input type="submit" value="<?= get_string('view_qualy_button', 'league') ?>"/>
    </form>
    
    
    <?php
    
}else if($rol == 'teacher'){
    ?>

<h1><?= get_string('teacher_panel', 'league') ?></h1>
<form action="management.php" method="get">
    <input type="hidden" name="id" value="<?= $cmid ?>" />
    <input type="hidden" name="lid" value="<?= $cm->id ?>" />
    <input type="hidden" name="uid" value="<?= $USER->id ?>" />
    <input type="submit" value="<?= get_string('manage_exercises_button', 'league') ?>"/>
</form>
<form action="qualy.php" method="get">
    <input type="hidden" name="id" value="<?= $cmid ?>" />
    <input type="submit" value="<?= get_string('view_qualy_button', 'league') ?>"/>
</form>
    

    <?php
}else{
    notice(get_string('noviewdiscussionspermission', 'league'));
}

echo $OUTPUT->footer();

?>