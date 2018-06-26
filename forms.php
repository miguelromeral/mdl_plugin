<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
//Formulario de Subida de archivos
class upload_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $maxbytes = 10000000;
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('hidden', 'id_exer', $this->_customdata['id_exer']);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->addElement('hidden', 'name', $this->_customdata['name']);
        $mform->addElement('hidden', 'statement', $this->_customdata['statement']);
        
        
        //$mform->addElement('filepicker', 'userfile', get_string('upload_exercise', 'league'), null,
        //           array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
        //$mform->addRule('userfile', null, 'required', null, 'client');
        
        $mform->addElement('filemanager', 'userfile', get_string('attachment', 'moodle'), null,
                    array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760,
                           'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
        //ECHAR UN OJO A LOS ACCEPTED_TYPES
        
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}

//Formulario de creación de ficheros
class exercise_form extends moodleform {
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
        $mform->addElement('hidden', 'id_exer', $id_exer);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_RAW);
        //Nombre del ejercicio
        $name = ($this->_customdata['name'] ? $this->_customdata['name'] : "");
        $mform->addElement('text', 'name', get_string('ae_name', 'league'), array('size'=>'50'));
        $mform->setDefault('name', $name);
        //Descripción del ejercicio
        $statement = ($this->_customdata['statement'] ? $this->_customdata['statement'] : "");
        $mform->addElement('textarea', 'statement', get_string("introtext", "survey"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setDefault('statement', $statement);
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}