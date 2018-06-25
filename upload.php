<!DOCTYPE html>
<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');

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
$PAGE->set_url('/mod/league/management.php', array('id' => $cm->id));

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
$PAGE->set_title(format_string(get_string('upload_title', 'league')));
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
$var="SELECT
c.id AS courseid,
c.fullname,
u.id as userid,
u.username,
u.firstname,
u.lastname,
u.email
                                
FROM
mdl_role_assignments ra
JOIN mdl_user u ON u.id = ra.userid
JOIN mdl_role r ON r.id = ra.roleid
JOIN mdl_context cxt ON cxt.id = ra.contextid
JOIN mdl_course c ON c.id = cxt.instanceid

WHERE ra.userid = u.id
                                
AND ra.contextid = cxt.id
AND cxt.contextlevel =50
AND cxt.instanceid = c.id
AND roleid = 5
and c.id = $cm->course
and userid = $USER->id

ORDER BY c.fullname";

$valido = $DB->get_records_sql($var);

if($valido == 0){
    ?>
        Por desgracia, no pertenece a este curso
    <?php
}else{
    //Indica si los datos del formulario son correctos.
    $correcto = false;
    if($_POST)
    {   
        echo "--> POST: <br>";
        print_r($_POST);
        echo "<-- <br>";
        if ($_POST['action'] == 'begin'){
            ?>
        
        <h1><?= $_POST['exer_name'] ?></h1>
        <div><?= $_POST['exer_description'] ?></div>
        
        <br>
        <form action="upload.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $cmid ?>" />
            <input type="hidden" name="action" value="upload_file" />
            <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
            <input type="file" name="file" size="50" />
            <br />
            <input type="submit" value="<?= get_string('upload_exercise_file', 'league') ?>" />

        </form>
        
        
            
            <?php
        }else if($_POST['action'] == 'upload_file'){
            
            print_r($_FILES);
            
            $targetfolder = "/tmp/";
            $targetfolder = $targetfolder . basename( $_FILES['file']['name']) ;
            $ok=1;
            $file_type=$_FILES['file']['type'];

            echo "-> Máximo -> ".ini_get('upload_max_filesize');
            
            if ($file_type=="application/pdf") {
                if(move_uploaded_file($_FILES['file']['tmp_name'], $targetfolder)){
                    echo "<strong>". get_string('ue_success', 'league') . " ( ".basename( $_FILES['file']['name'])." )</strong><br>";
                } else {
               echo "<strong>". get_string('ue_error_unknown', 'league') ."<br>".
                       get_string('ue_error_max_size', 'league') ." ".ini_get('upload_max_filesize')."</strong><br>";
                }
           } else {
               echo "<strong>". get_string('ue_error_type', 'league') ."<br>".
                       get_string('ue_error_max_size', 'league') ." ".ini_get('upload_max_filesize')."</strong><br>";
               
           }
        }
    }
}
    
echo $OUTPUT->footer();
