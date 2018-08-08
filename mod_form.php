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
 * Defines the form to create a league instance.
 *
 * @package    mod_league
 * @category   form
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/league/lib.php');

/**
 * The form to create (or modify) a league instance.
 *
 * @package    mod_league
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_league_mod_form extends moodleform_mod {
    
    /**
     * Define all form elements.
     */
    public function definition() {
        $mform = $this->_form;
        
        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        // League name
        $mform->addElement('text', 'name', get_string('league_name', 'league'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Possible options to sort the qualy:
        // by best marks or by more exercises.
        $options = array();
        $options[1] = get_string('select_best_marks', 'league');
        $options[2] = get_string('select_more_exercises', 'league');
        $mform->addElement('select', 'method', get_string('mod_form_select_method', 'league'), $options);
        $mform->setType('method', PARAM_INT);
        $mform->addRule('method', null, 'required', null, 'client');
        $mform->addHelpButton('method', 'select_method', 'league');
        
        // Sort description (or presentation) about the league.
        $mform->addElement('textarea', 'intro', get_string("mod_form_presentation", "league"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setType('intro', PARAM_TEXT);
        $mform->setDefault('intro', '');
        $mform->addRule('intro', null, 'required', null, 'client');
        
        // Features to the Course Module elements.
        $features = new stdClass();
        $features->groups           = false;
        $features->groupings        = false;
        $features->groupmembersonly = false;
        $this->standard_coursemodule_elements($features);
        
        // Add action buttons.
        $this->add_action_buttons();
    }
}