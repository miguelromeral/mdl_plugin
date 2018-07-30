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
 * Page which teacher can mark a student attempt.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get all files that we'll use.
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/classes/form/mark_form.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// Identifies the Course Module ID.
$cmid = optional_param('id', 0, PARAM_INT);
// Attempt ID.
$attemptid = required_param('attempt', PARAM_INT);

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
$PAGE->set_url('/mod/league/mark_student.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string(get_string('mark_title', 'league')));
$PAGE->set_heading(format_string($course->fullname));

// Create an instance of league. Usefull to check capabilities.
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo, $context, $league);

// Get and render the appropiate class to this page.
$output = $PAGE->get_renderer('mod_league');

// Retrieve attempt data from the database.
$attemptleague = \league_model::getDataFromAttempt($attemptid, 'league');
$attemptuser = \league_model::getDataFromAttempt($attemptid, 'id_user');
$exerciseid = \league_model::getDataFromAttempt($attemptid, 'exercise');

// Check if the attempt belongs to this league and user can mark students.
$sameleague = ($league->id == $attemptleague);
$canmark = $mod->usermarkstudents($USER->id);

$lastattempt = \league_model::is_last_attempt($attemptuser, $exerciseid, $attemptid);

if($canmark and $sameleague and $lastattempt){
    
    // Retrieve data to be printed in the form.
    $studentname = \league_model::get_student_name($attemptuser);
    $name = \league_model::getNameExerByID($exerciseid);
    $mark = \league_model::getDataFromAttempt($attemptid, 'mark');
    $observations = \league_model::getDataFromAttempt($attemptid, 'observations');
            
    // Create the mark form with the appropiate data.
    $params = array(
        'id'            => $cmid,
        'id_exer'       => $exerciseid,
        'mark'          => $mark,
        'name_exer'     => $name,
        'student'       => $studentname,
        'idat'          => $attemptid,
        'observations'  => $observations,
        'id_user'       => $attemptuser
    );
    $mform = new mod_league\form\mark_form(null, $params);

    if ($mform->is_cancelled()) {
        // If the form is cancelled, a render page to go back.
        //$panel = new mod_league\output\go_back_view($cmid, get_string('mark_cancel','league'), null, 'marking.php', array('exercise' => $attemptexercise));
        //echo $output->render($panel);
        
        redirect(new moodle_url('/mod/league/marking.php', array('id' => $cmid, 'exercise' => $exerciseid)));

    } else{ 
        
        echo $output->header();

        if ($data = $mform->get_data()) {
            // If the data are correct, we handle it.
            $newmark = $data->mark;
            $newobservations = $data->observations;

            // Update the attempt with the mark updated.
            $success = league_attempt_update_instance($league, $attemptid, $newmark, $newobservations, $exerciseid);

            if ($success != 0){
                // If the update was OK, trigger an event.
                $mod->trigger_attempt_graded_event($attemptid, $attemptuser, $exerciseid, $newmark);   
            }

            // Render a page to go back.
            $panel = new mod_league\output\go_back_view($cmid, get_string('mark_sent_success','league'), null, 'marking.php', array('exercise' => $exerciseid));
            echo $output->render($panel);

        } else {
            // Display the mark form.
            $mform->display();
        }
    }
}else{
    
    echo $output->header();

    // If the user cant mark students or the attempt ID doesn't belongs to this
    // league, render an error page.
    $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
    echo $output->render($panel);
}

// Print the footer page.
echo $output->footer();