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
 * Form where studentds can upload files to exercises.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_league\form;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Class upload_form
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('header', 'h1', $this->_customdata['name']);
        $mform->addElement('static', 'h2', $this->_customdata['statement']);
        
        $mform->addElement('hidden', 'exercise', $this->_customdata['id_exer']);
        $mform->setType('exercise', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'name', $this->_customdata['name']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('hidden', 'statement', $this->_customdata['statement']);
        $mform->setType('statement', PARAM_TEXT);
        
        $mform->addElement('filepicker', 'userfile', get_string('upload_exercise_file', 'league'), null,
                   array('maxbytes' => 10000000, 'accepted_types' => '*'));
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}