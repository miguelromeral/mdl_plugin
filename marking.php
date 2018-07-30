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
 * Page where teachers can select a student to be evaluated (and also download
 * their tasks done).
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get all files that we'll use.
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
require_once($CFG->dirroot.'/mod/league/classes/output/total_attempts_view.php');
require_once($CFG->dirroot.'/mod/league/classes/output/go_back_view.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// Identifies the Course Module ID.
$cmid = optional_param('id', 0, PARAM_INT);
// Get exercise ID.
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
$PAGE->set_url('/mod/league/marking.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Print title and header.
$PAGE->set_title(format_string(get_string('management_title', 'league')));
$PAGE->set_heading(format_string($course->fullname));

// Create an instance of league. Usefull to check capabilities.
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->get_cm($cmid);
$mod = new mod_league\league($cminfo, $context, $league);

// Get and render the appropiate class to this page.
$output = $PAGE->get_renderer('mod_league');
echo $output->header();

// Check if the user can mark studentds and the exercise ID belongs to this league.
$canmark = $mod->usermarkstudents($USER->id);
$validexercise = ($exerciseid == -1 || \league_model::isleagueexercise($exerciseid, $league->id));

if($canmark and $validexercise){
    // Retrieve all the students attempts to this exercises and render it.
    $attempts = \league_model::get_attempts_by_exercise($exerciseid);
    $panel = new mod_league\output\total_attempts_view($cmid, $attempts, $exerciseid, \league_model::getNameExerByID($exerciseid), $context->id);
    echo $output->render($panel);
    
    $panel = new mod_league\output\go_back_view($cmid);
    echo $output->render($panel);
}else{
    // If the usar cant have capabilities to mark, render an error page.
    $panel = new mod_league\output\go_back_view($cmid, get_string('notallowedpage','league'), get_string('nopermission','league'));
    echo $output->render($panel);
    
}

// Print the footer page.
echo $output->footer();