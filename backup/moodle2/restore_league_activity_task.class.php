<?php

 /**
 * league restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
 
require_once($CFG->dirroot . '/mod/league/backup/moodle2/restore_league_stepslib.php'); // Because it exists (must)
 
class restore_league_activity_task extends restore_activity_task {
 
    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }
 
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_league_activity_structure_step('league_structure', 'league.xml'));
    }
 
    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();
 
        $contents[] = new restore_decode_content('league', array('filearea'), 'league');
 
        return $contents;
    }
 
    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();
 
        $rules[] = new restore_decode_rule('LEAGUEVIEWBYID', '/mod/league/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('LEAGUEINDEX', '/mod/league/index.php?id=$1', 'course');
 
        return $rules;
 
    }
 
    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * league logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();
 
        $rules[] = new restore_log_rule('league', 'add', 'view.php?id={course_module}', '{league}');
        $rules[] = new restore_log_rule('league', 'update', 'view.php?id={course_module}', '{league}');
        $rules[] = new restore_log_rule('league', 'view', 'view.php?id={course_module}', '{league}');
        $rules[] = new restore_log_rule('league', 'choose', 'view.php?id={course_module}', '{league}');
        $rules[] = new restore_log_rule('league', 'choose again', 'view.php?id={course_module}', '{league}');
        $rules[] = new restore_log_rule('league', 'report', 'report.php?id={course_module}', '{league}');
 
        return $rules;
    }
 
    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();
 
        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('league', 'view all', 'index?id={course}', null,
                                        null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('league', 'view all', 'index.php?id={course}', null);
 
        return $rules;
    }
 
}