<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the steps lib of the backup.
 *
 * @package     mod_league
 * @category    backup
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * Define all the backup steps that will be used by the backup_league_activity_task.
 *
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_league_activity_structure_step extends backup_activity_structure_step {
 
    /**
     * Define steps to follow when a backup is performing.
     * 
     * @return type The root element (League), wrapped into standard activity structure.
     */
    protected function define_structure() {
        
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');
 
        // Define each element separated.
        $league = new backup_nested_element('league', array('id'), array(
            'name', 'presentation', 'filearea', 'intro', 'timemodified', 'method'));
 
        $exercises = new backup_nested_element('exercises');
 
        $exercise = new backup_nested_element('exercise', array('id'), array(
            'name', 'timemodified',
            'statement', 'enabled','league','published'));
 
        $attempts = new backup_nested_element('attempts');
 
        $attempt = new backup_nested_element('attempt', array('id'), array(
            'name', 'timemodified', 'intro', 'introformat', 'exercise', 
            'user', 'mark', 'observations', 'itemid', 'url', 'league', 'timecreated'));
        
        // Build the tree.
        $league->add_child($exercises);
        $exercises->add_child($exercise);
 
        $league->add_child($attempts);
        $attempts->add_child($attempt);
          
        // Define sources.
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
        
        // Define id annotations.
        $attempt->annotate_ids('user', 'user');
 
        // Define file annotations.
        $league->annotate_files('mod_league', 'exuplod', null);
        
        // Return the root element (League),
        // wrapped into standard activity structure.
        return $this->prepare_activity_structure($league);
    }
}