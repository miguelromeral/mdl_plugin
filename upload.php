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
$component='mod_league';
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
    
        $name_ex = required_param('name', PARAM_TEXT);
        $stam_ex = required_param('statement', PARAM_TEXT);
    
        $maxbytes = 10000000;
        $mform = new upload_form(null,
                    array('id'=>$cmid,
                        'id_exer'=>$id_exer,
                        'name'=>$name_ex,
                        'statement'=>$stam_ex,
                        'max_bytes'=>$maxbytes));
    
        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            ?>
                <h1><?= get_string('ue_cancel','league') ?></h1>
                <form action="view.php" method="get" >
                    <input type="hidden" name="id" value="<?= $cmid ?>" />
                    <input type="submit" value="<?= get_string('go_back', 'league') ?>"/>
                </form>
        
            <?php
        } else if ($data = $mform->get_data()) {
            
            $component = 'mod_league';
            $filearea = $league->filearea;
            $name = $mform->get_new_filename('userfile');
            
            if($name){
                $itemid = time();
                $success = $mform->save_stored_file('userfile', $context->id, $component, $filearea, $itemid);
                
                $fs = get_file_storage();
                
                if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
                    foreach ($files as $file) {
                        //Ha cambiado el make_pluginfile desde la 2.2 hasta la 3.0
                        //$mu = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());           
                        $contenthash = $file->get_contenthash();
                        $id_file = getIDFileFromContenthash($contenthash);
                        
                        
                        $url = $CFG->wwwroot;
                        $url .= "/pluginfile.php/";
                        $url .= ($file->get_contextid())."/";
                        $url .= ($file->get_component())."/";
                        $url .= ($file->get_filearea())."/";
                        $url .= ($file->get_itemid())."/";
                        $url .= $name;
                        
                        $exito = league_attempt_add_instance($course->id, $USER->id, $id_exer, $id_file, $url, $name, $league->id);

                        if($exito){
                            ?>
                            <?= get_string('ue_success','league') ?><br>
                                <form action="view.php" method="get">
                                    <input type="hidden" name="id" value="<?= $cmid ?>" />
                                    <input type="submit" value="<?= get_string('go_back', 'league') ?>"/>
                                </form>
                            <?php
                        }
                    }
                }
            }else{
                echo "<br><br>Mal, debes subir un puto archivo.<br><br>";
            }
        } else {
        ?>

            <h1><?= $name_ex ?></h1>
            <div><?= $stam_ex ?></div>
            <br>


        <?php
          //displays the form
          $mform->display();
        }
    }

    
echo $OUTPUT->footer();
