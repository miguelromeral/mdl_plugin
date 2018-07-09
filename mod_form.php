<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/league/lib.php');
 
class mod_league_mod_form extends moodleform_mod {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
 
        $mform = $this->_form;
        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        $mform->addElement('text', 'name', get_string('league_name', 'league'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $options = array();
        $options[1] = get_string('select_best_marks', 'league');
        $options[2] = get_string('select_more_exercises', 'league');
        $mform->addElement('select', 'method', get_string('mod_form_select_method', 'league'), $options);
        $mform->setType('method', PARAM_INT);
        $mform->addRule('method', 'MENSAJE DE ERROR', 'required', null, 'client');
        $mform->addHelpButton('method', 'select_method', 'league');
        
        $features = new object();
        $features->groups           = false;
        $features->groupings        = false;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}