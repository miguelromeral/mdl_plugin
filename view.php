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
 * Main view from League module. It prints the exercises availables for students
 * and settings about the exercises created for teacher and editing roles.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get all files that we'll use.
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/utilities.php');

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

// TODO: Trigger course_module_viewed event.

// Initialize $PAGE and set parameters.
$PAGE->set_url('/mod/league/view.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string($league->name));
$PAGE->set_heading(format_string($course->fullname));

// Add a button on the top of page to go to qualy page.
$qualybutton = '<form action="qualy.php" method="get">
                    <input type="hidden" name="id" value="'. $cmid .'" />
                    <input type="submit" value="'. get_string('view_qualy_button', 'league') .'"/>
                </form>';
$PAGE->set_button($qualybutton);

// Create an instance of league. Usefull to check capabilities.
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo,  $context);

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

        // Get all exercises enabled in this league.
        $exercises = get_exercises_from_id_by_user($league->id, $USER->id);

        // Get all marks for every exercise in this league.
        $marks = get_notas_alumno($league->id, $cmid, $USER->id, $context->id);

        // Once we have all necessary data, we render it.
        $panel = new main_student_view($league->presentation, $exercises, $cmid, $context->id, null, $marks,
                $mod->userdownloadfiles($USER->id), $mod->useruploadfiles($USER->id));
        echo $output->render($panel);
        
        break;
    
    case 'teacher':
    
        //////////////////////////////////////////////////////////////////////
        //                                                                  //
        //                       TEACHERS' VIEW                             //
        //                                                                  //
        //////////////////////////////////////////////////////////////////////
        
        // Prints, if needed, an alert due to a teacher action.
        $alert = null;
        // Get the action made from teacher.
        $action = optional_param('action', 'no-act', PARAM_TEXT);

        // If teacher made an action, we handle it here.
        if($action != 'no-act'){
            // Get the exercise ID from POST.
            $attemptexercise = required_param('id_exer', PARAM_INT);
            // Get the exercise name from POST.
            $exercisename = required_param('exer_name', PARAM_TEXT);
            // Get the exercise description from POST.
            $exercisedescription = required_param('exer_description', PARAM_TEXT);
            // Get the exercise enabled flag from POST.
            $exerciseenabled = required_param('exer_enabled', PARAM_INT);
            // Get the exercise published marks flag from POST.
            $exercisepublished = required_param('exer_published', PARAM_INT);
            // Get the league ID.
            $leagueid = $league->id;
            
            switch($action){
                case 'delete': // Delete an exercise if it's posible.
                    
                    //Indicates if the deleting was successfull.
                    $attemptid = false;
                    
                    // If the exercises is enabled, we can't delete it
                    if ($exerciseenabled == 0){
                        // Delete instance of the exercise given the ID.
                        $attemptid = league_exercise_delete_instance($attemptexercise, $context);
                    }

                    // If the deleting of exercises was successfull, we alert 
                    // the teacher.
                    if ($attemptid){
                        $alert = get_string('exercise_deleted', 'league');
                    } else {
                        $alert = get_string('exercise_not_deleted', 'league');
                    }
                    
                    break;
                
                case 'enable_disable': // Enable or disable exercise to students.
                
                    // We negate the currect exercise state
                    // (because we want to do the opposite of currents).
                    $changedflag = ($exerciseenabled == 0 ? 1 : 0);

                    // Update the exercise with de data given.
                    league_exercise_update_instance($league, null, $exercisename, $exercisedescription, $leagueid, $attemptexercise, $changedflag, $exercisepublished, $context);

                    // Depending on the operation we made, we print one alert.
                    if ($changedflag == 0){
                        $alert = get_string('exercise_disabled', 'league');
                    } else {
                        $alert = get_string('exercise_enabled', 'league');
                    }
                    
                    break;
                
                case 'publish': // Publish the current marks for this exercise.
                    
                    // We negate the currect exercise state
                    // (because we want to do the opposite of currents).
                    $changedflag = ($exercisepublished == 0 ? 1 : 0);

                    // Update the exercise with de data given.
                    league_exercise_update_instance($league, null, $exercisename, $exercisedescription, $leagueid, $attemptexercise, $exerciseenabled, $changedflag, $context);

                    // Depending on the operation we made, we print one alert.
                    if ($changedflag == 0){
                        $alert = get_string('currently_unpublished', 'league');
                    } else {
                        $alert = get_string('currently_published', 'league');
                    }
                    
                    break;

            }
            
        }

        // Finally, once we have the updated exercises, we recover them.
        $exercises = get_exercises_from_id($league->id);
        // Once we have all necessary data, we render it.
        $panel = new main_teacher_view($exercises, $cmid, $context->id, 
                $mod->usermarkstudents($USER->id), $alert);
        echo $output->render($panel);
    
        break;
    
    default:    // The user has no role allowed to see this page.
        
        // We render an error page to warn the user.
        $panel = new go_back_view(
                get_string('notallowedpage','league'), 
                get_string('nopermission','league'), 
                $cmid,
                'view.php');
        echo $output->render($panel);
}

// Print the footer page.
echo $output->footer();