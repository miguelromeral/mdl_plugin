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
 * Page that shows grades from this league.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get all files that we'll use.
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/utilities.php');
require_once($CFG->dirroot.'/mod/league/classes/output/student_grade_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/single_content_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/grade_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');


// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// Identifies the Course Module ID.
$cmid = optional_param('id', 0, PARAM_INT);

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
$PAGE->set_url('/mod/league/grade.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string(get_string('title_grade', 'league')));
$PAGE->set_heading(format_string($course->fullname));

// Create an instance of league. Usefull to check capabilities.
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo, $context, $league);

// There are two types of role, one is for students (They can see exercises
// availables, their marks, etc.) and the other one is for non-students (like
// teachers or admin), users that can manage exercises. We difference that roles
// with this variable.
$role = null;

// Check what kind of role the user logged in belongs.
if($mod->userview($USER->id)){
    
    // If the user can also manage exercise, then he is has a 'teacher role'.
    if($mod->usermanageexercises($USER->id)){
        $role = 'teacher';
    }else{
        $role = 'student';
    }
    
}

// Add a button on the top of page to go to individual marks.
if($role == 'teacher'){
    $qualybutton = '<form action="qualy.php" method="get">
                    <input type="hidden" name="id" value="'. $cmid .'" />
                    <input type="submit" value="'. get_string('view_qualy_button', 'league') .'"/>
                </form>';
    $PAGE->set_button($qualybutton);
}

// Get and render the appropiate class to this page.
$output = $PAGE->get_renderer('mod_league');
echo $output->header();

switch($role){
    case 'student':
        //////////////////////////////////////////////////////////////////////
        //                                                                  //
        //                       STUDENTS' VIEW                             //
        //                                                                  //
        //////////////////////////////////////////////////////////////////////
        
        // Retrieve all marks for the current user and print all of them.
        $marks = \league_model::get_notas_alumno($league->id, $cmid, $USER->id, $context->id);
        $panel = new mod_league\output\student_grade_view($cmid, $context->id, $marks, $mod->userdownloadfiles($USER->id));
        echo $output->render($panel);
        
        break;
    
    case 'teacher':
        //////////////////////////////////////////////////////////////////////
        //                                                                  //
        //                       TEACHERS' VIEW                             //
        //                                                                  //
        //////////////////////////////////////////////////////////////////////
        
        $panel = new mod_league\output\single_content_view(null, get_string('individual_marks','league'));
        echo $output->render($panel);
        
        // Retrieve exercises and marks for that exercise.
        $exercises = \league_model::get_exercises_from_id($league->id);
        $marks = \league_model::get_tabla_notas($league->id);

        // Array to store exercises names.
        $exercisesnames = array();
        // Arrays with the names of each column (in addition to these one, 
        // we'll add the exercises name later).
        $tablecolumns = array('userpic','student');
        // The same as above, but these ones are to display de headers.
        $tableheaders = array(get_string('image', 'league'), get_string('student', 'league'));
        // For each exercises name, add a new column.
        foreach($exercises as $e){
            array_push($tablecolumns, $e->name);
            array_push($tableheaders, $e->name);
            array_push($exercisesnames, $e->id);
        }

        // Get all data from each user WITH NO ORDER (another function will
        // do that).
        $rows = array();
        foreach ($marks as $mark){
            $mark = get_object_vars($mark);
            
            // Data to this row.
            $data = array();
            // User picture profile and name.
            $data[] = league_model::get_user_image($mark['id'], 40);
            
            
            $data[] = $mark['firstname'] . " " . $mark['lastname'];

            // For each exercise we set the appropiate mark.
            foreach($exercisesnames as $exercise){
                // We search through the array to the mark that belongs to
                // this exercise.
                $hasmark = false;
                foreach($mark['notas'] as $outcome){
                    
                    if($outcome->exercise == $exercise){
                        $nota = $outcome->mark;
                        $hasmark = true;
                        
                        if($nota == -1){
                            // Attempt sent but no graded.
                            $data[] = "0 (".get_string('no_mark_yet','league').")";
                        }else{
                            // Attempt already graded.
                            $data[] = $outcome->mark . " %";
                        }

                    }
                }
                
                if(!$hasmark){
                    // Attempt no even sent.
                    $data[] = "(". get_string('not_done','league'). ")";
                }
            }
            // Add this row to all rows.
            array_push($rows, $data);
        }

        // Once we have all data, print everything with renderer.
        $panel = new mod_league\output\grade_view($rows, $tablecolumns, $tableheaders, $exercisesnames, $PAGE->url);
        echo $output->render($panel);

        break;
    
    default:    // The user has no role allowed to see this page.
        
        // We render an error page to warn the user.
        $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
        echo $output->render($panel);
}

// Print the footer page.
echo $output->footer();