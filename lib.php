<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/eventslib.php');

function league_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:          return true;
        default: return null;
    }
}

/**
 * Saves a new instance of the newmodule into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $league Submitted data from the form in mod_form.php
 * @param mod_newmodule_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted newmodule record
 */
function league_add_instance(stdClass $league, mod_league_mod_form $mform = null) {
    global $DB;
    $league->timemodified = time();
    $league->gradeweighting = 100;
    $league->filearea = 'exuplod';
    //print_r($league);
    
    $league->id = $DB->insert_record('league', $league);
    //Creamos Gradebook
    league_grade_item_update($league);
    
    
    
    
    $event = \mod_league\event\league_created::create(array(
        'objectid' => $league->id,
        'context' => context_module::instance($league->coursemodule)
    ));
    
    $event->trigger();
    
    return $league->id;
}

/**
 * Updates an instance of the newmodule in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $league An object from the form in mod_form.php
 * @param mod_newmodule_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function league_update_instance(stdClass $league, mod_league_mod_form $mform = null) {
    global $DB;
    $league->timemodified = time();
    $league->id = $league->instance;
    // You may have to add extra stuff in here.
    $result = $DB->update_record('league', $league);
    league_grade_item_update($league);
    return $result;
}

/**
 * Removes an instance of the newmodule from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function league_delete_instance($id) {
    global $DB;
    if (! $league = $DB->get_record('league', array('id' => $id))) {
        return false;
    }
    // Delete any dependent records here.
    $DB->delete_records('league', array('id' => $league->id));
    league_exercise_delete_instance($league->id);
    league_attempt_delete_instance($league->id);
    //newmodule_grade_item_delete($league);
    return true;
}


function league_exercise_add_instance($course, $name, $statement, $league) {
    global $DB;
    $record = new stdClass();
    $record->name = $name;
    $record->timemodified = time();
    $record->statement = $statement;
    $record->league = $league;
    $record->enabled = 0;
    $record->published = 0;
  
    $id = $DB->insert_record('league_exercise', $record);
   
    if($id){
        return true;
    }else{
        return false;
    }
}

function league_exercise_update_instance($leagueinstance, $course, $name, $statement, $league, $idexer, $enabled, $pub) {
    global $DB;
    $record = new stdClass();
    $record->id = $idexer;
    $record->name = $name;
    $record->timemodified = time();
    $record->statement = $statement;
    $record->league = $league;
    $record->enabled = $enabled;
    $record->published = $pub;
    $id = $DB->update_record('league_exercise', $record);
    league_update_grades($leagueinstance);
    
    if($id){
        return true;
    }else{
        return false;
    }
}

function league_exercise_delete_instance($id) {
    global $DB;
    if (! $exercise = $DB->get_record('league_exercise', array('league' => $id))) {
        return false;
    }
    $DB->delete_records('league_exercise', array('league' => $exercise->id));
    return true;
}

function league_attempt_add_instance($course, $id_user, $exercise, $id_file, $url, $name, $league) {
    global $DB;
    $record = new stdClass();
    $time = time();
    $record->name = ($name ? $name : "$id_user-$exercise-$time");
    $record->timemodified = $time;
    $record->id_user = $id_user;
    $record->exercise = $exercise;
    $record->mark = -1;
    $record->id_file = $id_file;
    $record->url = $url;
    $record->league = $league;
  
    $id = $DB->insert_record('league_attempt', $record);
   
    if($id){
        return true;
    }else{
        return false;
    }
}

function league_attempt_update_instance($league, $idat, $mark, $observations, $idexer) {
    global $DB;
    $record = new stdClass();
    $record->id = $idat;
    //$record->course = $course;
    $time = time();
    //$record->name = "$id_user-$exercise-$time";
    $record->timemodified = $time;
    $record->observations = $observations;
    //$record->id_user = $id_user;
    //$record->intro = null;
    //$record->introformat = null;
    //$record->exercise = $exercise;
    //$record->task = $task;
    $record->mark = $mark;
    
    //echo "Voy a añadir:<br>";
    //print_r($record);
    //echo "<br>----<br>";
  
    $id = $DB->update_record('league_attempt', $record);
    if(publishedMarks($idexer)){
        league_update_grades($league);
    }
    if($id){
        return true;
    }else{
        return false;
    }
}

function league_attempt_delete_instance($id) {
    global $DB;
    if (! $exercise = $DB->get_record('league_attempt', array('league' => $id))) {
        return false;
    }
    deleteFileAttempt();
    $DB->delete_records('league_attempt', array('league' => $id));
    return true;
}




////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////


/*
 * Debería actualizara los grades del usuario indicado. Esto opdría ser tan simple como obtener los grades
 * de un usuario para una actividad (con sus tablas específicas del modulo) y entonces llamar a league_grade_item_update()
 * 
 * $league : instancia del modulo
 * $userid : un ID específico o 0 para todos.
 * $nullifnone : si solo se especifica un usuario en concreto. Si es true y el usuario no tiene grade entonces un grade item
 * con un rawgrade nulo deberá ser insertado.
 */
function league_update_grades($league, $userid=0, $nullifnone=true){
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
 
    
        //Si sí es gradable, trata de recuperar las grades de los usuarios
        //league_get_user_grades pertenece a la Rating API, pero podemos acceder a las tablas
        // de nuestra actividad.
    if ($grades = league_get_user_grades($league, $userid)) {
        league_grade_item_update($league, $grades);
 
        //Si el usuario no tiene notas (o el usuario es 0) y $nullifnone entonces se inserta una nota NULL
    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        league_grade_item_update($league, $grade);
 
    } else {
        league_grade_item_update($league);
    }
}

/*
 *          ***********************************
 *          *                                 *
 *          *   ***********  **               *
 *          *   ***********  **         **    *
 *          *   **       **  **      ***      *
 *          *   **       **  **    ***        *
 *          *   **       **  *** ***          *
 *          *   **       **  *****            *
 *          *   **       **  **  ***          *
 *          *   ***********  **     ***       *
 *          *   ***********  **       ***     *
 *          *                                 *
 *          ***********************************
 *           (Aparentemente)
 */
//Debería crear o actualizar el grade item para una instancia de actividad llamando a grade_update().
//Puede actualizar tanto el grade de la actividad como del grade de usuario si se les proporciona el parametro $grades.
//Normalmente, $grades acepta el string 'reset' que seignifica que los grades en el gradebook deben ser reiniciados.
function league_grade_item_update($league, $grades=NULL){
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }
 
    $params = array('itemname'=>$league->name);
    if (isset($league->cmidnumber)) {
        //cmidnumber may not be always present
        $params['idnumber'] = $league->cmidnumber;
    }
    
    $params['gradetype'] = GRADE_TYPE_VALUE;
    //$params['grademax']  = $league->gradeweighting;
    $params['grademax']  = 100;
    $params['grademin']  = 0;

 
    //HABRÍA QUE ARREGLAR ESTO:
    /*
    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }*/
    return grade_update('mod/league', $league->course, 'mod', 'league', $league->id, 0, $grades, $params);
}

////////////////////////////////////////////////////////////////////////////////
// Rating API                                                              //
////////////////////////////////////////////////////////////////////////////////

//Devuelve las notas delos usuarios. IMPLEMENTAR SI USERID = 0
function league_get_user_grades($league, $userid = 0){
    global $DB;
    require_once('utilities.php');
    $grades = array();
    if($userid != 0){
        $notas = getArrayMarkByStudent($league->id, $userid, false);
        $n = sizeof($notas);
        $t = array_sum($notas);
            $media = ($n ? (int) $t / $n : 0);
        $grades[$userid] = (object)array('userid'=>$userid, 'rawgrade'=>$media);
    }else{
        $var="SELECT DISTINCT u.id AS userid, c.id AS courseid, u.firstname, u.lastname, u.username
        FROM mdl_user u
        JOIN mdl_user_enrolments ue ON ue.userid = u.id
        JOIN mdl_enrol e ON e.id = ue.enrolid
        JOIN mdl_role_assignments ra ON ra.userid = u.id
        JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
        JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id
        JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student'
        WHERE e.status = 0 AND u.suspended = 0 AND u.deleted = 0
          AND (ue.timeend = 0 OR ue.timeend > NOW()) AND ue.status = 0 and c.id = $league->course";
        $data = $DB->get_records_sql($var);
        foreach($data as $user){
            $d = get_object_vars($user);
            $uid = $d['userid'];
            $notas = getArrayMarkByStudent($league->id, $uid, false);
            $n = sizeof($notas);
            $t = array_sum($notas);
            $media = ($n ? (int) $t / $n : 0);
            $grades[$uid] = (object)array('userid'=>$uid, 'rawgrade'=>$media);
        }
    }
    return $grades;
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                              //
////////////////////////////////////////////////////////////////////////////////

function mod_league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options);
}

function league_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false; 
    }
   if ($filearea !== 'exuplod') {
        return false;
    }
    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    //require_login($course, true, $cm);
    require_login();
 
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/league:view', $context)) {
        return false;
    }
 
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = (int) array_shift($args);
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_league', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }
    
    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering. 
    // From Moodle 2.3, use send_stored_file instead.
    send_stored_file($file, 0, 0, true); // download MUST be forced - security!
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

function league_extend_navigation(navigation_node $leaguenode, stdclass $course, stdclass $module, cm_info $cm) {
    global $CFG, $DB, $PAGE;
    
    //$coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);
    $thingnode = $leaguenode->add(get_string('qualy_title', 'league'),
            new moodle_url('/mod/league/qualy.php', array('id' => $leaguenode->key)));
    $thingnode->make_active();
    
}

/**
 * Extends the settings navigation with the Hotpot settings
*/
function league_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $leaguenode=null) {
}