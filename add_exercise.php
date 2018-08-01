<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * View to display a form to add an exercise.
 * 
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/classes/form/exercise_form.php');
require_once($CFG->dirroot.'/mod/league/classes/output/single_content_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// Identifies the Course Module ID.
$cmid = required_param('id', PARAM_INT);
$exerciseid = required_param('exercise', PARAM_INT);

// Check if a course module exists.
if ($cmid) {
    
    // Get all the course module info belongs to league module.
    if (!$cm = get_coursemodule_from_id('league', $cmid)) {
        print_error(get_string('coursemoduleiidincorrect','league'));
    }
    
    // Get all course info given the course module.
    if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
        print_error(get_string('coursemodulemisconfigured','league'));
    }
    
    // Get all league info given the instance ID.
    if (!$league = $DB->get_record('league', array('id'=> $cm->instance))) {
        print_error(get_string('coursemoduleincorrect','league'));
    }
    
} else {
    // If not, a warning is showed.
    print_error('missingparameter');
}

// Check login and get context.
require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/league:view', $context);

// Initialize $PAGE and set parameters.
$PAGE->set_url('/mod/league/add_exercise.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string(get_string('add_new_exercise', 'league')));
$PAGE->set_heading(format_string($course->fullname));

// Create an instance of league. Usefull to check capabilities.
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo, $context, $league);

// Get and render the appropiate class to this page.
$output = $PAGE->get_renderer('mod_league');

// Check the if the user can manage exercises and the
// exercise belongs to the current league.
$canmanage = $mod->usermanageexercises($USER->id);
$exerciseinleague = \league_model::is_league_exercise($exerciseid, $league->id);

// If the exercise is -1 (that means is new) we can go on.
if($canmanage && ($exerciseid == -1 || $exerciseinleague)){
  
    
    $name = ($exerciseid == -1 ? '' : \league_model::get_data_from_exercise($exerciseid, 'name'));
    $description = ($exerciseid == -1 ? '' : \league_model::get_data_from_exercise($exerciseid, 'statement'));

    // Form to modify exercise.
    $mform = new mod_league\form\exercise_form(null,
            array('id'=>$cmid,
                'id_exer'=>$exerciseid,
                'name'=>$name,
                'statement'=>$description));

    if ($mform->is_cancelled()) {
        // If form is cancelled, go to the view.php
        redirect(new moodle_url('/mod/league/view.php', array('id' => $cmid)));
        
    } else {
        // Output the renderer header.
        echo $output->header();

        // If the data is valid:
        if ($formdata = $mform->get_data()) {
            // Get the appropiate data.
            $name = $formdata->name;
            $statement = $formdata->statement;
            $course = $cm->course;
            
            // If the exercise is new, add an instance of it.
            if($exerciseid == -1){
                
                $idexernuevo = league_exercise_add_instance($name, $statement, $league->id);
                if($idexernuevo){
                    // Trigger the event.
                    $mod->trigger_exercise_created_event($idexernuevo);
                    $attemptid = true;
                }else{
                    $attemptid = false;
                }

            }else{
                // If the exercise is old, update the instance.
                $attemptid = league_exercise_update_instance($name, $statement, $league->id, $exerciseid, 0, 0);
                $mod->trigger_exercise_updated_event($exerciseid);
            }

            // Print a renderer if all went ok.
            if($attemptid != 0){
                $panel = new mod_league\output\go_back_view($cmid, get_string('ae_success','league'));
                echo $output->render($panel);
            }
        } else {
            // Display the form.
            $panel = new mod_league\output\single_content_view(get_string('ae_warning','league'));
            echo $output->render($panel);

            if($exerciseid != -1){
                // Delete an exercise (it has been disabled first).
                echo '<p>
                    <form action="view.php" method="post" >
                    <input type="hidden" name="id" value="'. $cmid. '" />
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id_exer" value="'. $exerciseid .'" />
                    <input type="submit" value="'.get_string('del', 'league').'"/>
                </form></p>';
            }
            
            //displays the form
            $mform->display();
        }
    }
}else{
    // If the user can not manage exercise, print an error.
    echo $output->header();

    $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
    echo $output->render($panel);
}

// Print the footer page.
echo $output->footer();