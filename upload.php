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
 * Page where students can upload the task files corresponding to
 * the answers from one exercise.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get all files that we'll use.
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/locallib.php');
require_once($CFG->dirroot.'/mod/league/classes/form/upload_form.php');
require_once($CFG->dirroot.'/mod/league/classes/output/single_content_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/utilities.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// Identifies the Course Module ID.
$cmid = optional_param('id', 0, PARAM_INT);
// Identifies the exercise the user's task upload belongs
$attemptexercise = required_param('exercise', PARAM_INT);  

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
$contextid = $context->id;
require_capability('mod/league:view', $context);

// Initialize $PAGE and set parameters.
$PAGE->set_url('/mod/league/upload.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string(get_string('upload_title', 'league')));
$PAGE->set_heading(format_string($course->fullname));

// Create an instance of league. Usefull to check capabilities.
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo, $context, $league);

// Get and render the appropiate class to this page.
$output = $PAGE->get_renderer('mod_league');
        
// If the user can upload files and the exercise ID belongs to the current league,
// user can upload the file (it's recommended only students are available to
// upload files).
if($mod->useruploadfiles($USER->id) && isleagueexercise($attemptexercise, $league->id)){

    // Get the name and statement from exercise ID:
    $exercisename = getNameExerByID($attemptexercise);
    $exercisestatement = getNameExerByID($attemptexercise, false);

    // Create the upload form with aproppiate data.
    $params = array(
        'id'        => $cmid,
        'id_exer'   => $attemptexercise
    );
    $mform = new mod_league\form\upload_form(null, $params);

    if ($mform->is_cancelled()) {
        // If form is cancelled render a page to back to main view.
        redirect(new moodle_url('/mod/league/view.php', array('id' => $cmid)));
        
    } else if ($data = $mform->get_data()) {
        
        echo $output->header();
        
        // If the data is correct, we handle it.
        
        // Get all components to store a file.
        $component = 'mod_league';
        $filearea = $league->filearea;
        $filename = $mform->get_new_filename('userfile');

        // If the user has uploaded a file:
        if($filename){
            // Create a unique ID to this file (the item ID).
            $itemid = \league_model::generateRandomFileID();
            
            // Store the file with the all the necessary data.
            $attemptid = $mform->save_stored_file('userfile', $context->id, $component, $filearea, $itemid);

            // Create and attempt once the file was stored.
            $fs = get_file_storage();
            
            if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
                foreach ($files as $file) {
                    // Obtain the content file ID (different from item ID)
                    $contenthash = $file->get_contenthash();
                    $fileid = \league_model::getIDFileFromContenthash($contenthash);

                    // Create the attempt in the database.
                    $attemptid = league_attempt_add_instance($course->id, $USER->id, $attemptexercise, $file->get_itemid(), null, $filename, $league->id, $context);

                    // If everything is OK in the database, we trigger the event
                    // and warn the user that's OK.
                    if($attemptid){
                        
                        // Trigger the attempt submitted event.
                        league_attempt_submitted($attemptexercise, $attemptid, $context);
                        
                        // Render a page to go back to main menu.
                        $panel = new mod_league\output\go_back_view($cmid, get_string('ue_success','league'));
                        echo $output->render($panel);
                    }
                }
            }
            
        }else{
            
            // If there is no file uploaded, we warn the user to upload
            // a valid file.
            $panel = new mod_league\output\go_back_view($cmid, get_string('ue_no_file','league'), null, 
                    'upload.php', array('exercise' => $attemptexercise));
            echo $output->render($panel);
        }
    } else {
        
        echo $output->header();
        
        // Print the exercises name and statement.
        $panel = new mod_league\output\single_content_view($exercisestatement, $exercisename);
        echo $output->render($panel);
        
        // Displays the upload form.
        $mform->display();
    }
}else{
    // If the user has no upload capabilities, render an error page.
    $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
    echo $output->render($panel);
}

// Print the footer page.
echo $output->footer();
