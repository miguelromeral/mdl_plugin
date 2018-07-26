<?php

namespace mod_league\form;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

//Formulario de creación de ficheros
class exercise_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form;
        $id_exer = ($this->_customdata['id_exer'] ? $this->_customdata['id_exer'] : -1);
        
        if($id_exer == -1){
            $mform->addElement('header', 'header_form', get_string('add_exercise_title','league'));
        }else{
            $mform->addElement('header', 'header_form', get_string('modify_exercise_title','league'));
        }
        //ID ejercicio
        $mform->addElement('hidden', 'exercise', $id_exer);
        $mform->setType('exercise', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_RAW);
        //Nombre del ejercicio
        $name = (empty($this->_customdata['name']) ? "" : $this->_customdata['name']);
        $mform->addElement('text', 'name', get_string('ae_name', 'league'), array('size'=>'50'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->setDefault('name', $name);
        //Descripción del ejercicio
        $statement = (empty($this->_customdata['statement']) ? "" : $this->_customdata['statement']);
        $mform->addElement('textarea', 'statement', get_string("ae_description", "league"), 'wrap="virtual" rows="20" cols="50"');
        $mform->addRule('statement', null, 'required', null, 'client');
        $mform->addRule('statement', get_string('maximumchars', '', 4096), 'maxlength', 4096, 'client');
        $mform->setDefault('statement', $statement);
        
        $mform->addElement('static', 'warning', get_string('ae_warning','league'));
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}