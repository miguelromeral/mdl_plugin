<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
class mod_league_mod_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
 
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('html', '<h2>Estos son de prueba:</h2>');
        
        $mform->addElement('text', 'email', get_string('fill_course', 'league')); // Add elements to your form
        $mform->setType('email', PARAM_NOTAGS);                   //Set type of element
        $mform->setDefault('email', 'Please enter email');        //Default value
        
        
        $mform->addElement('html', '<h1>Aquí empieza lo de verdad:</h1>');
        
        
        $options = array();
        
        $result = $DB->get_records_sql('SELECT * FROM {course} WHERE id > ?', array('1')); //El 1 no, que es el moodle completo.
        
        //$mform->addElement('html', '<div>Resultado: '. print_r($result) .'</div>');
        
        
        // ID => NOMBRE DEL CURSO
        $options[0] = get_string('default_selected_course', 'league');
        foreach ($result as $rowclass)
        {
            $rowclass = json_decode(json_encode($rowclass), True);
            $options[$rowclass["id"]] = $rowclass["fullname"];
        }
        $mform->addElement('select', 'select_course_activity', get_string('mod_form_select_course', 'league'), $options);
        $mform->addHelpButton('select_course_activity', 'select_course_activity', 'league');

        
        
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}