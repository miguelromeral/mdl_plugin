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
$filearea='userfile';
$options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 50,
        'accepted_types' => array('image', 'document', 'application/pdf', 'application/zip', 'presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.template'));        

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

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
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
    
        $attachment = $id_exer."-".$USER->id."-att";
    
        $maxbytes = 10000000;
        $mform = new upload_form(null,
                    array('id'=>$cmid,
                        'id_exer'=>$id_exer,
                        'name'=>$_POST['name'],
                        'statement'=>$_POST['statement'],
                        'at_name'=>$attachment,
                        'max_bytes'=>$maxbytes));
    
        if (empty($entry->id)) {
            $entry = new stdClass;
            //$entry->id = 0;
            //$entry->id = null;
            $entry->id = $cmid;
        }

        $options = array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1);
        $draftitemid = file_get_submitted_draft_itemid('attachments');

        file_prepare_draft_area($draftitemid, $context->id, 'mod_league', 'attachment', $entry->id,
                                $options);

        $entry->attachments = $draftitemid;

        $mform->set_data($entry);
    
        
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
            $file = file_save_draft_area_files($data->attachments, $context->id, 'mod_league', 'attachment',
                   $entry->id, $options);
            $name = $id_exer."_".$USER->id."_".time()."-".$mform->get_new_filename('attachments');
            $folder = "/home/league";
            $fullpath = $folder."/".$name;
            attempt_add_instance($course->id, $USER->id, $id_exer, null, $name);
            
            ?>
            <?= get_string('ue_success','league') ?><br>
                <form action="view.php" method="get">
                    <input type="hidden" name="id" value="<?= $cmid ?>" />
                    <input type="submit" value="<?= get_string('go_back', 'league') ?>"/>
                </form>
            <?php
        } else {
        ?>

            <h1><?= $_POST['name'] ?></h1>
            <div><?= $_POST['statement'] ?></div>
            <br>


        <?php
          //displays the form
          $mform->display();
        }
    }

    
echo $OUTPUT->footer();
