<?php

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

// Course ID.
$id = optional_param('id', 0, PARAM_INT);

if($id){
    // Ensure that the course specified is valid
    if (!$course = $DB->get_record('course', array('id'=> $id))) {
        print_error(get_string('courseidincorrect', 'league'));
    }
}


require_course_login($course);
$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/league/index.php', array('id' => $id));
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');


$PAGE->navbar->add(get_string('modulenameplural','league'));
$PAGE->set_title(format_string($course->shortname . ": " . get_string('modulenameplural','league')));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('leagues_in_course', 'league') . ": " . $course->fullname, 2);

// Start of the table for General Forums.
$generaltable = new html_table();
$generaltable->head  = array (get_string('league','league'), get_string('presentation','league'), get_string('exercises','league'));
$generaltable->align = array ('left', 'left', 'center');


$leagues = \league_model::get_league_from_course($id);

foreach($leagues as $league){
    $league = get_object_vars($league);
    
    $data = array();

    $url= new \moodle_url('/mod/league/view.php', array(
        'id' => \league_model::get_context_module_id_from_league($league['id']),
        ));


    $data[] = '<a href="'.$url.'">'. $league['name'] ."</a>";
               
    
    $data[] = $league['presentation'];
    
    
    $exercises = \league_model::get_exercises_from_id($league['id']);
    
    $count = count($exercises);
    
    $data[] = $count;
                        
    
    
    $generaltable->data[] = $data;
    
}


echo html_writer::table($generaltable);


echo $OUTPUT->footer();