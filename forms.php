<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
//Formulario de Subida de archivos
class upload_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('hidden', 'id_exer', $this->_customdata['id_exer']);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->addElement('hidden', 'name', $this->_customdata['name']);
        $mform->addElement('hidden', 'statement', $this->_customdata['statement']);
        
        $attachment = $this->_customdata['at_name'];
        
        $mform->addElement('filemanager', 'attachments', get_string('upload_exercise_file', 'league'), null,
                    array('subdirs' => 0, 'maxbytes' => $this->_customdata['max_bytes'], 'areamaxbytes' => 10485760,
                        'maxfiles' => 1, 'accepted_types' => array('.txt', '.pdf', '.doc'), 
                        /*'return_types'=> FILE_INTERNAL | FILE_EXTERNAL*/));
        
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
        $name = (empty($this->_customdata['name']) ? "" : $this->_customdata['name']);
        $mform->addElement('text', 'name', get_string('ae_name', 'league'), array('size'=>'50'));
        $mform->setDefault('name', $name);
        //Descripción del ejercicio
        $statement = (empty($this->_customdata['statement']) ? "" : $this->_customdata['statement']);
        $mform->addElement('textarea', 'statement', get_string("introtext", "survey"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setDefault('statement', $statement);
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}

//Formulario de creación de ficheros
class mark_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form;
        $id_exer = ($this->_customdata['id_exer'] ? $this->_customdata['id_exer'] : -1);
        $id_user = ($this->_customdata['id_user'] ? $this->_customdata['id_user'] : -1);
        $mark = ($this->_customdata['mark'] ? $this->_customdata['mark'] : -1);
        
        $result = $DB->get_records_sql('SELECT * FROM {exercise} WHERE id = ?', array($id_exer)); //El 1 no, que es el moodle completo.
        $name_exer = "";
        foreach ($result as $rowclass)
        {
            $rowclass = json_decode(json_encode($rowclass), True);
            $name_exer = $rowclass["name"];
        }
        
        
        $mform->addElement('header', 'header_form', $name_exer);
       
        //ID ejercicio
        $mform->addElement('hidden', 'id_exer', $id_exer);
        $mform->addElement('hidden', 'id_user', $id_user);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->addElement('hidden', 'idat', $this->_customdata['idat']);
        $mform->addElement('hidden', 'name', $this->_customdata['name_exer']);
        $mform->setType('id', PARAM_RAW);
        
        $options[-1] = "Sin calificar";
        for ($i = 0; $i <= 100; $i++)
        {
            $options[$i] = "$i";
        }
        $mform->addElement('select', 'mark', get_string('set_mark', 'league'), $options);
        $mform->setType('mark', PARAM_INT);
        $mform->addRule('mark', 'MENSAJE DE ERROR', 'required', null, 'client');
        $mform->setDefault('mark', $options[$mark]);
        
        $mform->addElement('textarea', 'observations', get_string("set_observation", "league"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setDefault('observations', "");
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}