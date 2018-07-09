<?php
/**
 * Structure step to restore one league activity
 */
class restore_league_activity_structure_step extends restore_activity_structure_step {
 
    protected function define_structure() {
 
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');
 
        $paths[] = new restore_path_element('league', '/activity/league');
        $paths[] = new restore_path_element('league_exercise', '/activity/league/exercises/exercise');
        if ($userinfo) {
            $paths[] = new restore_path_element('league_attempt', '/activity/league/attempts/attempt');
        }
 
        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }
 
    protected function process_league($data) {
        global $DB;
 
        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
 
        // insert the league record
        $newitemid = $DB->insert_record('league', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
 
    protected function process_league_exercise($data) {
        global $DB;
 
        $data = (object)$data;
        $oldid = $data->id;
 
        //print_r($data);
        
        $data->league = $this->get_new_parentid('league');
 
        $newitemid = $DB->insert_record('league_exercise', $data);
        $this->set_mapping('league_exercise', $oldid, $newitemid);
    }
 
    protected function process_league_attempt($data) {
        global $DB;
 
        $data = (object)$data;
 
        //print_r($data);
        
        $data->league = $this->get_new_parentid('league');
        $data->exercise = $this->get_mappingid('league_exercise', $data->exercise);
        $data->id_user = $this->get_mappingid('user', $data->id_user);
 
        $newitemid = $DB->insert_record('league_attempt', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }
 
    protected function after_execute() {
        // Add league related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_league', 'filearea', null);
    }
}