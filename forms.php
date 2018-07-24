<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
//Formulario de Subida de archivos
class upload_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('header', 'h1', $this->_customdata['name']);
        $mform->addElement('static', 'h2', $this->_customdata['statement']);
        
        $mform->addElement('hidden', 'id_exer', $this->_customdata['id_exer']);
        $mform->setType('id_exer', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'name', $this->_customdata['name']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('hidden', 'statement', $this->_customdata['statement']);
        $mform->setType('statement', PARAM_TEXT);
        
        $mform->addElement('filepicker', 'userfile', get_string('upload_exercise_file', 'league'), null,
                   array('maxbytes' => $this->_customdata['max_bytes'], 'accepted_types' => '*'));
        
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
        $mform->setType('id_exer', PARAM_INT);
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

//Formulario de creación de ficheros
class mark_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        
        $mform = $this->_form;
        $id_exer = ($this->_customdata['id_exer'] ? $this->_customdata['id_exer'] : -1);
        $id_user = ($this->_customdata['id_user'] ? $this->_customdata['id_user'] : -1);
        $mark = ($this->_customdata['mark'] ? $this->_customdata['mark'] : -1);
        $obs = ($this->_customdata['observations'] ? $this->_customdata['observations'] : "");
        
        $result = $DB->get_records_sql('SELECT * FROM {league_exercise} WHERE id = ?', array($id_exer)); //El 1 no, que es el moodle completo.
        $name_exer = "";
        foreach ($result as $rowclass)
        {
            $rowclass = json_decode(json_encode($rowclass), True);
            $name_exer = $rowclass["name"];
        }
        
        
        $mform->addElement('header', 'header_form', $name_exer);
       
        //ID ejercicio
        $mform->addElement('hidden', 'id_exer', $id_exer);
        $mform->setType('id_exer', PARAM_INT);
        $mform->addElement('hidden', 'id_user', $id_user);
        $mform->setType('id_user', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'idat', $this->_customdata['idat']);
        $mform->setType('idat', PARAM_INT);
        $mform->addElement('hidden', 'name', $this->_customdata['name_exer']);
        $mform->setType('name', PARAM_TEXT);
        
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
        $mform->setType('observations', PARAM_TEXT);
        $mform->setDefault('observations', $obs);
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}