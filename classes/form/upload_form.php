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
 * Form where students can upload files to exercises.
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
 * The form to upload an attempt.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_form extends \moodleform {
    
    /**
     * Define all form elements.
     */
    public function definition() {
        $mform = $this->_form;
        
        // Exercise ID and Course Module hiden.
        $mform->addElement('hidden', 'exercise', $this->_customdata['id_exer']);
        $mform->setType('exercise', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
        
        // Filepicker to get the user uploaded file.
        // All types availables.
        $mform->addElement('filepicker', 'userfile', get_string('upload_exercise_file', 'league'), null,
                   array('accepted_types' => '*'));
        $mform->addRule('userfile', null, 'required', null, 'client');
        
        // Add action buttons.
        $this->add_action_buttons();
    }
}