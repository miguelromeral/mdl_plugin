<?php

namespace mod_league\form;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

//Formulario de creación de ficheros
class mark_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form;
        $id_exer = ($this->_customdata['id_exer'] ? $this->_customdata['id_exer'] : -1);
        $id_user = ($this->_customdata['id_user'] ? $this->_customdata['id_user'] : -1);
        $mark = ($this->_customdata['mark'] ? $this->_customdata['mark'] : -1);
        $obs = ($this->_customdata['observations'] ? $this->_customdata['observations'] : "");
        
        $mform->addElement('header', 'header_form', 
                get_string('mark_title','league').": ".
                $this->_customdata['student']." (".
                $this->_customdata['name_exer'].")");
       
        //ID ejercicio
        $mform->addElement('hidden', 'exercise', $id_exer);
        $mform->setType('exercise', PARAM_INT);
        $mform->addElement('hidden', 'id_user', $id_user);
        $mform->setType('id_user', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'attempt', $this->_customdata['idat']);
        $mform->setType('attempt', PARAM_INT);
        $mform->addElement('hidden', 'name', $this->_customdata['name_exer']);
        $mform->setType('name', PARAM_TEXT);
        
        $options[-1] = get_string('no_mark_yet','league');
        for ($i = 0; $i <= 100; $i++)
        {
            $options[$i] = "$i";
        }
        $mform->addElement('select', 'mark', get_string('set_mark', 'league'), $options);
        $mform->setType('mark', PARAM_INT);
        $mform->addRule('mark', get_string('no_mark_error', 'league'), 'required', null, 'client');
        $mform->setDefault('mark', $options[$mark]);
        
        $mform->addElement('textarea', 'observations', get_string("set_observation", "league"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setType('observations', PARAM_TEXT);
        $mform->setDefault('observations', $obs);
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}