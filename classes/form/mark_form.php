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
 * Defines the form to set the mark to an student attempt.
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
 * The form to set the mark to an attempt student.
 *
 * @package    mod_league
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mark_form extends \moodleform {
    
    /**
     * Define all form elements.
     */
    public function definition() {
        $mform = $this->_form;
        
        // Check the exercise and user ID exists.
        $exercises = ($this->_customdata['id_exer'] ? $this->_customdata['id_exer'] : -1);
        $user = ($this->_customdata['id_user'] ? $this->_customdata['id_user'] : -1);
        
        // Get the current mark and observations to the attempt.
        $mark = ($this->_customdata['mark'] ? $this->_customdata['mark'] : -1);
        $obs = ($this->_customdata['observations'] ? $this->_customdata['observations'] : "");
        
        // Custom header element.
        $mform->addElement('header', 'header_form', 
                get_string('mark_title','league').": ".
                $this->_customdata['student']." (".
                $this->_customdata['name_exer'].")");
       
        // Exercise ID, user ID, attempt ID, exercise name and Course Module ID hiden.
        $mform->addElement('hidden', 'exercise', $exercises);
        $mform->setType('exercise', PARAM_INT);
        
        $mform->addElement('hidden', 'id_user', $user);
        $mform->setType('id_user', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'attempt', $this->_customdata['idat']);
        $mform->setType('attempt', PARAM_INT);
        
        $mform->addElement('hidden', 'name', $this->_customdata['name_exer']);
        $mform->setType('name', PARAM_TEXT);
        
        // Set all possible marks to set.
        $options[-1] = get_string('no_mark_yet','league');
        for ($i = 0; $i <= 100; $i++)
        {
            $options[$i] = "$i";
        }
        // Add all possible marks to the form.
        $mform->addElement('select', 'mark', get_string('set_mark', 'league'), $options);
        $mform->setType('mark', PARAM_INT);
        $mform->addRule('mark', get_string('no_mark_error', 'league'), 'required', null, 'client');
        $mform->setDefault('mark', $options[$mark]);
        
        // Textarea to set the observations to the attempt.
        $mform->addElement('textarea', 'observations', get_string("set_observation", "league"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setType('observations', PARAM_TEXT);
        $mform->setDefault('observations', $obs);
        
        // Add action buttons.
        $this->add_action_buttons();
    }
}