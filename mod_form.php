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
        
        $result = $DB->get_records_sql('SELECT * FROM {course} WHERE id > ?', array('1')); //El 1 no, que es el moodle completo.
        
        // ID => NOMBRE DEL CURSO
        //$options[0] = get_string('default_selected_course', 'league');
       /* foreach ($result as $rowclass)
        {
            $rowclass = json_decode(json_encode($rowclass), True);
            $options[$rowclass["id"]] = $rowclass["fullname"];
        }
        $mform->addElement('select', 'course', get_string('mod_form_select_course', 'league'), $options);
        $mform->setType('course', PARAM_INT);
        $mform->addRule('course', 'MENSAJE DE ERROR', 'required', null, 'client');
        $mform->addHelpButton('course', 'select_course_activity', 'league');
        
        * 
        */
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}