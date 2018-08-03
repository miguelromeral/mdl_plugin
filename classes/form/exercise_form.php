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
 * Defines the form to create an exercise.
 *
 * @package    mod_league
 * @category   form
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_league\form;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * The form to create (or modify) an exercise.
 *
 * @package    mod_league
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exercise_form extends \moodleform {
    
    /**
     * Define all form elements.
     */
    public function definition() {
        $mform = $this->_form;
        
        // Exercise number (-1 if is new).
        $exercise = ($this->_customdata['id_exer'] ? $this->_customdata['id_exer'] : -1);
        
        // Print appropiate header.
        if($exercise == -1){
            $mform->addElement('header', 'header_form', get_string('add_new_exercise','league'));
        }else{
            $mform->addElement('header', 'header_form', get_string('modify_exercise_title','league'));
        }
        
        // Exercise ID and CMID hiden.
        $mform->addElement('hidden', 'exercise', $exercise);
        $mform->setType('exercise', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_RAW);
        
        // Exercise Name.
        $name = (empty($this->_customdata['name']) ? "" : $this->_customdata['name']);
        $mform->addElement('text', 'name', get_string('ae_name', 'league'), array('size'=>'50'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->setDefault('name', $name);
        
        // Exercise statement.
        $statement = (empty($this->_customdata['statement']) ? "" : $this->_customdata['statement']);
        $mform->addElement('textarea', 'statement', get_string("description", "league"), 'wrap="virtual" rows="20" cols="50"');
        $mform->addRule('statement', null, 'required', null, 'client');
        $mform->addRule('statement', get_string('maximumchars', '', 4096), 'maxlength', 4096, 'client');
        $mform->setDefault('statement', $statement);
        
        // Add action buttons.
        $this->add_action_buttons();
    }
}