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
 * Page where users can view the global qualyfing respect all the students.
 * Students should have capabilities to see RESTRICTED QUALY 'seerestrictedqualy'
 * (@see /mod/league/db/access.php) and the teacers and manager of the course 
 * should have capability 'seequaly', which allow to see all users data in the
 * table.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/classes/output/qualy_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/classes/qualy.php');

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
$PAGE->set_url('/mod/league/qualy.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string($league->name." - ".get_string('qualy_title', 'league')));
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

// Check if user can see FULL qualy (in that case, it's a 'teacher').
if($mod->userseequaly($USER->id)){
    $role = 'teacher';
    // If the user can't see the qualy, instead can see RESTRICTED one,
    // user is an 'student'.
}else if($mod->userseerestrictedqualy($USER->id)){
    $role = 'student';
}

// If a teacher can mark students, a button is enabled for him.
if($role == 'teacher' and $mod->usermarkstudents($USER->id)){
    $qualybutton = '<form action="grade.php" method="get">
                    <input type="hidden" name="id" value="'. $cmid .'" />
                    <input type="submit" value="'. get_string('view_individual_marks', 'league') .'"/>
                </form>';
    $PAGE->set_button($qualybutton);
}

// Get and render the appropiate class to this page.
$output = $PAGE->get_renderer('mod_league');
echo $output->header();

// Retrieve ordered qualy from this league.
$qc = new \league_qualy($league->id, $course->id, $role, $league->method);
$q = $qc->get_qualy();

// If the user role is defined, print the table.
if ($role == 'student' || $role == 'teacher'){
    
    // Get panel in function of role type.
    $panel = new mod_league\output\qualy_view(get_string('qualy_title', 'league'), $cmid, $q, $USER->id, $role);
    echo $output->render($panel);
    
    if($role == 'teacher'){
        // If the user is a teacher, He also can see the qualy like the studentds.
        // The main difference is that teacher is allowed to see data like studentds
        // name while students only can see his names.
        $qsc = new \league_qualy($league->id, $course->id, 'student', $league->method);
        $qs = $qsc->get_qualy();
        
        $panel = new mod_league\output\qualy_view(get_string('qts', 'league'), $cmid, $qs, $USER->id, $role);
        echo $output->render($panel);
    }
    
}else{
    // If the user has no permission to see the qualy, render a error page.
    $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
    echo $output->render($panel);
}

// Print the footer page.
echo $output->footer();