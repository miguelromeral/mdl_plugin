<?php

require_once($CFG->dirroot . '/mod/league/backup/moodle2/backup_league_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/league/backup/moodle2/backup_league_settingslib.php'); // Because it exists (optional)
 
/**
 * League backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_league_activity_task extends backup_activity_task {
 
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
        $this->add_step(new backup_league_activity_structure_step('league_structure', 'league.xml'));
        // League only has one structure step
    }
 
    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;
 
        $base = preg_quote($CFG->wwwroot,"/");
 
        // Link to the list of leagues
        $search="/(".$base."\/mod\/league\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@LEAGUEINDEX*$2@$', $content);
 
        // Link to league view by moduleid
        $search="/(".$base."\/mod\/league\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@LEAGUEVIEWBYID*$2@$', $content);
 
        return $content;
    }
    
    
}