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
        if ($_POST['action'] == 'begin'){
            ?>
        
        <h1><?= $_POST['exer_name'] ?></h1>
        <div><?= $_POST['exer_description'] ?></div>
        
        <br>
        <!--<form action="upload.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $cmid ?>" />
            <input type="hidden" name="action" value="upload_file" />
            <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
            <input type="file" name="file" size="50" />
            <br />
            <input type="submit" value="<?= get_string('upload_exercise_file', 'league') ?>" />

        </form>
        -->
        <?php
        
        $mform = new upload_form();
        
        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            //Handle form cancel operation, if cancel button is present on form
        } else if ($fromform = $mform->get_data()) {
          //In this case you process validated data. $mform->get_data() returns data posted in form.
        } else {
          // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
          // or on the first display of the form.

          //Set default data (if any)
          $mform->set_data($toform);
          //displays the form
          $mform->display();
        }
        
        
            
        }else if($_POST['action'] == 'upload_file'){
            
            //print_r($_POST);
            
            //echo "<br>--> User ID: $USER->id";
            
            $ejerc = null;
            if($_POST['id_exer']){
                $ejerc = $_POST['id_exer'];
            }
             
            print_r($_FILES);
            
            if($_FILES["file"]["name"]){
                $filename = $_FILES["file"]["name"];
                $source = $_FILES["file"]["tmp_name"];
                $type = $_FILES["file"]["type"];

                $name = explode(".", $filename);
                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                foreach($accepted_types as $mime_type) {
                        if($mime_type == $type) {
                                $okay = true;
                                break;
                        } 
                }
                
                $continue = strtolower($name[1]) == 'zip' ? true : false;
                if(!$continue) {
                        $message = "The file you are trying to upload is not a .zip file. Please try again.";
                }

                $target_path = "/tmp/".$filename;  // change this to the correct site path
                if(move_uploaded_file($source, $target_path)) {
                        $zip = new ZipArchive();
                        $x = $zip->open($target_path);
                        if ($x === true) {
                                $zip->extractTo("/tmp/"); // change this to the correct site path
                                $zip->close();
                                unlink($target_path);
                        }
                        //call to lib function
                        $message = "Your .zip file was uploaded and unpacked.";
                } else {	
                        $message = "There was a problem with the upload. Please try again.";
                }
                
                echo "<strong>$message</strong>";
            }
            
            
        }
    }
}
    
echo $OUTPUT->footer();
