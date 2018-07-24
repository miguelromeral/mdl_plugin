<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');
require_once('forms.php');

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$idat = required_param('attempt', PARAM_INT);

require_login($course, true, $cm);

$PAGE->set_url('/mod/league/mark_student.php', array('id' => $cm->id));

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
$PAGE->set_title(format_string(get_string('mark_title', 'league')));
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

if (!has_capability('mod/league:view', $context)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}

$attleague = getDataFromAttempt($idat, 'league');
$id_user = getDataFromAttempt($idat, 'id_user');
$id_exer = getDataFromAttempt($idat, 'exercise');

//Comprobaciones de lógica:
$sameleague = ($league->id == $attleague);

if($mod->usermarkstudents($USER->id) && $sameleague){
    
    $result = $DB->get_records_sql('SELECT * FROM {user} WHERE id = ?', array($id_user));
    $alumno = "";
    foreach ($result as $rowclass)
    {
        $rowclass = json_decode(json_encode($rowclass), True);
        $alumno = $rowclass['firstname'] ." ".$rowclass['lastname'];
    }
    $name_exer = getNameExerByID($id_exer);
    $mark = getDataFromAttempt($idat, 'mark');
    $observations = getDataFromAttempt($idat, 'observations');
            
        $mform = new mark_form(null,
                    array('id'=>$cmid,
                        'id_exer'=>$id_exer,
                        'mark'=>$mark,
                        'name_exer'=>$name_exer,
                        'student' => $alumno,
                        'idat'=>$idat,
                        'observations'=>$observations,
                        'id_user'=>$id_user));
    
        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            
            $panel = new go_back_view(
                    get_string('mark_cancel','league'), null, $cmid, 'marking.php',
                    array('exercise' => $id_exer));
            echo $output->render($panel);
            
        } else if ($data = $mform->get_data()) {
            $new_mark = $data->mark;
            $new_observaciones = $data->observations;
            
            league_attempt_update_instance($league, $idat, $new_mark, $new_observaciones, $id_exer);
            
            $event = \mod_league\event\attempt_graded::create(array(
                'objectid' => $idat,
                'relateduserid' => $id_user,
                'other' => array('exercise' => $id_exer,
                                'mark' => $new_mark),
                'context' => $context
            ));
            $event->trigger();
            
            $panel = new go_back_view(
                    get_string('mark_sent_success','league'), null, $cmid, 'marking.php',
                    array('exercise' => $id_exer));
            echo $output->render($panel);
            
        } else {
            
            $mform->display();
        }
    
}else{
    $panel = new fail_view(
            get_string('notallowedpage','league'), 
            get_string('nopermission','league'), 
            $cmid);
    echo $output->render($panel);
}

echo $output->footer();