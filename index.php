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
 * View to display all league instances in a course.
 * 
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// Course ID.
$id = optional_param('id', 0, PARAM_INT);

if($id){
    // Ensure that the course specified is valid
    if (!$course = $DB->get_record('course', array('id'=> $id))) {
        print_error(get_string('courseidincorrect', 'league'));
    }
}

// Check login and get context.
require_course_login($course);
$coursecontext = context_course::instance($course->id);

// Initialize $PAGE and set parameters.
$PAGE->set_url('/mod/league/index.php', array('id' => $id));
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');

// Print title and header.
$PAGE->navbar->add(get_string('modulenameplural','league'));
$PAGE->set_title(format_string($course->shortname . ": " . get_string('modulenameplural','league')));
$PAGE->set_heading(format_string($course->fullname));

// Output general renderer.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('leagues_in_course', 'league') . ": " . $course->fullname, 2);

// Start of the table for General Leagues.
$generaltable = new html_table();
$generaltable->head  = array (get_string('league','league'), get_string('presentation','league'), get_string('exercises','league'));
$generaltable->align = array ('left', 'left', 'center');

// Get all league isntances given an Course ID.
$leagues = \league_model::get_league_from_course($id);

// For each league, add a row.
foreach($leagues as $league){
    $league = get_object_vars($league);
    
    $data = array();

    // URL to redirect to that league when clicking.
    $url= new \moodle_url('/mod/league/view.php', array(
        'id' => \league_model::get_context_module_id_from_league($league['id']),
        ));
    $data[] = '<a href="'.$url.'">'. $league['name'] ."</a>";
               
    // League presentation.
    $data[] = $league['presentation'];
    
    // Number of exercises.
    $exercises = \league_model::get_exercises_from_id($league['id']);
    $count = count($exercises);
    $data[] = $count;
                        
    $generaltable->data[] = $data;
}

// Print the table.
echo html_writer::table($generaltable);

// Print the footer page.
echo $OUTPUT->footer();