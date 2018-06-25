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
        
        $mform->addElement('text', 'action', "TMP: Accion: (upload_file)", array('size'=>'64'));
        $mform->setType('action', PARAM_TEXT);
        $mform->addRule('action', null, 'required', null, 'client');
        $mform->addElement('filepicker', 'userfile', get_string('upload_exercise', 'league'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
        $mform->addRule('action', null, 'required', null, 'client');
        
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}