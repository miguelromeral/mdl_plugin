<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/locallib.php');
require_once($CFG->dirroot.'/mod/league/classes/form/exercise_form.php');
require_once($CFG->dirroot.'/mod/league/classes/output/single_content_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/utilities.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$attemptexercise = required_param('exercise', PARAM_INT);    // ID Ejercicio (-1 si no hay)
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);

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

$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo, $context, $league);

$output = $PAGE->get_renderer('mod_league');

echo $output->header();

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!has_capability('mod/league:view', $context, $USER->id)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}



if($mod->usermanageexercises($USER->id) && ($attemptexercise == -1 || isleagueexercise($attemptexercise, $league->id))){
  
    $filename = ($attemptexercise == -1 ? '' : getNameExerByID($attemptexercise));
    $description = ($attemptexercise == -1 ? '' : getNameExerByID($attemptexercise, false));


    $mform = new mod_league\form\exercise_form(null,
            array('id'=>$cmid,
                'id_exer'=>$attemptexercise,
                'name'=>$filename,
                'statement'=>$description));


    //Form processing and displaying is done here
    if ($mform->is_cancelled()) {
        
        $msg = ($attemptexercise == -1 ? 'ae_cancel_new' : 'ae_cancel');
        
        $panel = new mod_league\output\go_back_view($cmid, get_string($msg,'league'));
        echo $output->render($panel);
        
    } else if ($formdata = $mform->get_data()) {
        //$errores = "";
        $filename = $formdata->name;
        /*if(strlen($name) > 255 || empty($name)){
            $errores .= (get_string('ae_error_name','league') . "<br>");
        }*/
        $statement = $formdata->statement;
        /*if(empty($statement)){
            $errores .= get_string('ae_error_description','league') . "<br>";
        }
*/
  //      if(empty($errores)){
            $course = $cm->course;
            if($attemptexercise == -1){
                
                $idexernuevo = league_exercise_add_instance($course, $filename, $statement, $league->id, $USER->id, $context);
                if($idexernuevo){
                    // Trigger the event.
                    league_exercise_created($league->id, $idexernuevo, $context);
                    $correcto = true;
                }else{
                    $correcto = false;
                }
                
            }else{
                $correcto = league_exercise_update_instance($league, $course, $filename, $statement, $league->id, $attemptexercise, 0, 0, $context);
            }

            if($correcto){
                
                $panel = new mod_league\output\go_back_view($cmid, get_string('ae_success','league'));
                echo $output->render($panel);
                
            }
    } else {
        
        $panel = new mod_league\output\single_content_view(get_string('ae_warning','league'));
        echo $output->render($panel);
        
      //displays the form
      $mform->display();
    }

}else{
    $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
    echo $output->render($panel);
}

echo $output->footer();