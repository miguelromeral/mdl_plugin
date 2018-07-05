<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');
require_once('./forms.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$id_exer = required_param('id_exer', PARAM_INT);    // ID Ejercicio (-1 si no hay)
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
$PAGE->set_url('/mod/league/add_exercise.php', array('id' => $cm->id));

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

//Pone como diseño el estandar de Moodle
$PAGE->set_pagelayout('standard');

// Mark viewed if required
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Print header.
$PAGE->set_title(format_string(get_string('add_exercise_title', 'league')));
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

// Recuperamos el ID del profesor y del modulo, si no coinciden, se mostrará un aviso para que salga.
$var="SELECT c.id as course, c.shortname, u.id as teacher, u.username, u.firstname || ' ' || u.lastname AS name FROM mdl_course c LEFT OUTER JOIN mdl_context cx ON c.id = cx.instanceid LEFT OUTER JOIN mdl_role_assignments ra ON cx.id = ra.contextid AND ra.roleid = '3' LEFT OUTER JOIN mdl_user u ON ra.userid = u.id WHERE cx.contextlevel = '50' AND c.id = $cm->course AND u.id = $USER->id";

$valido = $DB->get_records_sql($var);

if($valido == 0){
    ?>
        Por desgracia, no pertenece a este curso
    <?php
}else{
    
        
        $name = optional_param('name', '', PARAM_TEXT);
        $description = optional_param('statement', '', PARAM_TEXT);
    
     
        $mform = new exercise_form(null,
                array('id'=>$cmid,
                    'id_exer'=>$id_exer,
                    'name'=>$name,
                    'statement'=>$description));
        
        
        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            ?>
                <h1><?= get_string('ae_cancel','league') ?></h1>
                <form action="view.php" method="get" >
                    <input type="hidden" name="id" value="<?= $cmid ?>" />
                    <input type="submit" value="<?= get_string('go_back', 'league') ?>"/>
                </form>
        
            <?php
        } else if ($formdata = $mform->get_data()) {
            $errores = "";
            $name = $formdata->name;
            if(strlen($name) > 255 || empty($name)){
                $errores .= (get_string('ae_error_name','league') . "<br>");
            }
            $statement = $formdata->statement;
            if(empty($statement)){
                $errores .= get_string('ae_error_description','league') . "<br>";
            }

            if(empty($errores)){
                $course = $cm->course;
                if($id_exer == -1){
                    $correcto = league_exercise_add_instance($course, $name, $statement, $league->id);
                }else{
                    $correcto = league_exercise_update_instance($league, $course, $name, $statement, $league->id, $id_exer, 0, 0);
                }
                
                if($correcto){
                    ?>
                    <?= get_string('ae_success','league') ?><br>
                        <form action="view.php" method="get">
                            <input type="hidden" name="id" value="<?= $cmid ?>" />
                            <input type="submit" value="<?= get_string('manage_exercises_button', 'league') ?>"/>
                        </form>
                    <?php
                }
            }else{
                 ?>
            <div>
                <?= get_string('ae_errors','league') ?><br>
                <strong><?php echo $errores ?></strong><br>
                <form action="add_exercise.php" method="get">
                    <input type="hidden" name="id" value="<?= $cmid ?>" />
                    <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
                    <input type="hidden" name="name" value="<?= $name ?>" />
                    <input type="hidden" name="statement" value="<?= $description ?>" />
                    <input type="submit" value="<?= get_string('go_back', 'league') ?>"/>
                </form>
            </div>
                <?php
            }
        } else {
          //displays the form
          $mform->display();
        }

}
    
echo $OUTPUT->footer();
