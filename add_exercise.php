<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');
require_once('./forms.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$id_exer = required_param('exercise', PARAM_INT);    // ID Ejercicio (-1 si no hay)
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
$cm_info = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cm_info,  context_module::instance($cm->id));

$output = $PAGE->get_renderer('mod_league');

echo $output->header();

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!has_capability('mod/league:view', $context, $USER->id)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}



if($mod->usermanageexercises($USER->id) && ($id_exer == -1 || isleagueexercise($id_exer, $league->id))){
  
    $name = ($id_exer == -1 ? '' : getNameExerByID($id_exer));
    $description = ($id_exer == -1 ? '' : getNameExerByID($id_exer, false));


    $mform = new exercise_form(null,
            array('id'=>$cmid,
                'id_exer'=>$id_exer,
                'name'=>$name,
                'statement'=>$description));


    //Form processing and displaying is done here
    if ($mform->is_cancelled()) {
        
        $msg = ($id_exer == -1 ? 'ae_cancel_new' : 'ae_cancel');
        
        $panel = new go_back_view(
                get_string($msg,'league'), null, $cmid, 'view.php');
        echo $output->render($panel);
        
    } else if ($formdata = $mform->get_data()) {
        //$errores = "";
        $name = $formdata->name;
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
            if($id_exer == -1){
                $correcto = league_exercise_add_instance($course, $name, $statement, $league->id, $USER->id, $context);
            }else{
                $correcto = league_exercise_update_instance($league, $course, $name, $statement, $league->id, $id_exer, 0, 0, $context);
            }

            if($correcto){
                
                $panel = new go_back_view(
                        get_string('ae_success','league'), null, $cmid, 'view.php');
                echo $output->render($panel);
                
            }
     /*   }else{
             ?>
        <div>
            <?= get_string('ae_errors','league') ?><br>
            <strong><?php echo $errores ?></strong><br>
            <form action="add_exercise.php" method="get">
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
                <input type="submit" value="<?= get_string('go_back', 'league') ?>"/>
            </form>
        </div>
            <?php
        }*/
    } else {
      //displays the form
      $mform->display();
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