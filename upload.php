<?php

require_once('../../config.php');
require_once('lib.php');
require_once('utilities.php');
require_once('./forms.php');

defined('MOODLE_INTERNAL') || die();

//Identifica la actividad específica (o recurso)
$cmid = required_param('id', PARAM_INT);    // Course Module ID
$id_exer = required_param('exercise', PARAM_INT);  
$cm = get_coursemodule_from_id('league', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$info = get_fast_modinfo($course);
//print_object($info);
$component='mod_league';

require_login($course, true, $cm);
/*
 * ABSOLUTAMENTE NECESARIO PONER EL URL.
 * Por lo menos, el id, después se pueden poner otras 'key' => 'value'
 * Convierte todo lo que le pasamos a un objeto moodle_url
 */
$PAGE->set_url('/mod/league/upload.php', array('id' => $cm->id));

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
$contextid = $context->id;
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

$output = $PAGE->get_renderer('mod_league');

echo $output->header();

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!has_capability('mod/league:view', $context)) {
    notice(get_string('noviewdiscussionspermission', 'league'));
}

$modinfo = get_fast_modinfo($course);
$cm_info = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cm_info,  context_module::instance($cm->id));


if($mod->useruploadfiles($USER->id) && isleagueexercise($id_exer, $league->id)){

    $name_ex = getNameExerByID($id_exer);
    $stam_ex = getNameExerByID($id_exer, false);

    $maxbytes = 10000000;
    $mform = new upload_form(null,
                array('id'=>$cmid,
                    'id_exer'=>$id_exer,
                    'name'=>$name_ex,
                    'statement'=>$stam_ex,
                    'max_bytes'=>$maxbytes));

    //Form processing and displaying is done here
    if ($mform->is_cancelled()) {
        
        $panel = new go_back_view(
                get_string('ue_cancel','league'), null, $cmid, 'view.php');
        echo $output->render($panel);

    } else if ($data = $mform->get_data()) {

        $component = 'mod_league';
        $filearea = $league->filearea;
        $name = $mform->get_new_filename('userfile');

        if($name){
            $itemid = generateRandomFileID();
            $success = $mform->save_stored_file('userfile', $context->id, $component, $filearea, $itemid);

            $fs = get_file_storage();

            if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
                foreach ($files as $file) {
                    //Ha cambiado el make_pluginfile desde la 2.2 hasta la 3.0
                    //$mu = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());           
                    $contenthash = $file->get_contenthash();
                    $id_file = getIDFileFromContenthash($contenthash);


                    $url = getURLFile($file->get_contextid(), $file->get_component(), 
                            $file->get_filearea(), $file->get_itemid(), $name);

                    $exito = league_attempt_add_instance($course->id, $USER->id, $id_exer, $file->get_itemid(), $url, $name, $league->id, $context);

                    if($exito){
                        $panel = new go_back_view(
                                get_string('ue_success','league'), null, $cmid, 'view.php');
                        echo $output->render($panel);
                    }
                }
            }
        }else{
            
            $panel = new go_back_view(
                    get_string('ue_no_file','league'), null, $cmid, 'upload.php',
                    array('exercise' => $id_exer));
            echo $output->render($panel);
        }
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
