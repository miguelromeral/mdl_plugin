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
    
    ?>
<h1><?= get_string('main_panel_student','league') ?></h1>
<h2><?= get_string('availables_exercises','league') ?></h2>
    <?php
    //echo $output->inicio_estudiante();
    
    print_exercises($league->id, 'student', $cmid);
    
    ?>
<h2><?= get_string('my_marks','league') ?></h2>
    <?php
    
    print_notas_alumno($league->id, $cmid, $USER->id);
    
    
    ?>
    
    
    <form action="qualy.php" method="get">
        <input type="hidden" name="id" value="<?= $cmid ?>" />
        <input type="submit" value="<?= get_string('view_qualy_button', 'league') ?>"/>
    </form>
    
    
    <?php
    
}else if($rol == 'teacher'){
    
    ?>
    
    <h1><?= get_string('h_manag_exer','league') ?></h1>
        
    <?php
    
    if($_POST)
    {
        $id_exer_post = $_POST['id_exer'];
        $exer_name_post = $_POST['exer_name'];
        $exer_description_post = $_POST['exer_description'];
        $exer_enabled_post = $_POST['exer_enabled'];
        $pub = $_POST['exer_published'];
        $course = $cm->course;
        $league_post = $league->id;

        if ($_POST['action'] == 'delete'){
            
            $exito = false;
            
            //Si está deshabilitado, podremos eliminarlo
            if ($exer_enabled_post == 0){
                $exito = league_exercise_delete_instance($id_exer_post);
            }
            
            ?>
            <div>
                <strong><?php
                if ($exito){
                    echo get_string('exercise_deleted', 'league');
                } else {
                    echo get_string('exercise_not_deleted', 'league');
                }
                ?></strong><br>
            </div>
            <?php
            
        } else if ($_POST['action'] == 'enable_disable'){

            //Negamos el cambio, si estába des, lo activamos, y si estaba activado, lo des.
            $cambio = ($exer_enabled_post == 0 ? 1 : 0);
            
            league_exercise_update_instance($league, $course, $exer_name_post, $exer_description_post, $league_post, $id_exer_post, $cambio, $pub);

            ?>
            <div>
                <strong><?php
                if ($cambio == 0){
                    echo get_string('exercise_disabled', 'league');
                } else {
                    echo get_string('exercise_enabled', 'league');
                }
                ?></strong><br>
            </div>
            <?php
        } else if ($_POST['action'] == 'publish'){

            //Negamos la accion qeu esta ahora
            $cambio = ($pub == 0 ? 1 : 0);
            league_exercise_update_instance($league, $course, $exer_name_post, $exer_description_post, $league_post, $id_exer_post, $exer_enabled_post, $cambio);

            ?>
            <div>
                <strong><?php
                if ($cambio == 0){
                    echo get_string('currently_unpublished', 'league');
                } else {
                    echo get_string('currently_published', 'league');
                }
                ?></strong><br>
            </div>
            <?php
        }
        
    }
    
    //Aquí mostraremos una lista con todas las actividades
    print_exercises($league->id, 'teacher', $cmid);

    ?>
    
    <form action="add_exercise.php" method="get">
        <input type="hidden" name="id" value="<?= $cmid ?>" />
        <input type="hidden" name="id_exer" value="-1" />
        <input type="submit" value="<?= get_string('add_exercise_button', 'league') ?>"/>
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