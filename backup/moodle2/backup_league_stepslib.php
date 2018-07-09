<?php
 
/**
 * Define all the backup steps that will be used by the backup_league_activity_task
 */
class backup_league_activity_structure_step extends backup_activity_structure_step {
 
    protected function define_structure() {
        
// To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 
        
        
// Define each element separated
        $league = new backup_nested_element('league', array('id'), array(
            'name', 'filearea', 'timemodified',
            'gradeweighting', 'method'));
 
        $exercises = new backup_nested_element('exercises');
 
        $exercise = new backup_nested_element('exercise', array('id'), array(
            'name', 'timemodified',
            'statement', 'enabled','league','published'));
 
        $attempts = new backup_nested_element('attempts');
 
        $attempt = new backup_nested_element('attempt', array('id'), array(
            'name', 'timemodified', 'intro', 'introformat',
            'exercise', 'id_user', 'mark', 'observations', 'id_file', 'url', 'league'));
        
        
// Build the tree
        $league->add_child($exercises);
        $exercises->add_child($exercise);
 
        $league->add_child($attempts);
        $attempts->add_child($attempt);
          
// Define sources
        $league->set_source_table('league', array('id' => backup::VAR_ACTIVITYID));
 
        $exercise->set_source_sql('
            SELECT *
              FROM {league_exercise}
             WHERE league = ?',
            array(backup::VAR_PARENTID));
 
        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $attempt->set_source_sql('
                SELECT *
                  FROM {league_attempt}
                 WHERE league = ?',
                array(backup::VAR_PARENTID));
        }
        
        
// Define id annotations
        $attempt->annotate_ids('user', 'id_user');
 
        
        
// Define file annotations
        $league->annotate_files('mod_league', 'filearea', null);
        
        
// Return the root element (League), wrapped into standard activity structure
        return $this->prepare_activity_structure($league);
    }
}