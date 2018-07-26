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
 * Defines the restore_league_activity_task class.
 *
 * @package     mod_league
 * @category    restore
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();


/**
 * Structure step to restore one league activity.
 * 
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_league_activity_structure_step extends restore_activity_structure_step {
 
    /**
     * Defines the structure of the data that have to restore.
     * 
     * @return type The paths wrapped into standard activity structure.
     */
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
        $this->set_mapping('league', $oldid, $newitemid);
    }
 
    protected function process_league_exercise($data) {
        global $DB;
 
        $data = (object)$data;
        $oldid = $data->id;
 
        $data->league = $this->get_new_parentid('league');
 
        $newitemid = $DB->insert_record('league_exercise', $data);
        $this->set_mapping('league_exercise', $oldid, $newitemid);
    }
 
    protected function process_league_attempt($data) {
        global $DB;
 
        $data = (object)$data;       
        $oldid = $data->id; 
        
        $data->league = $this->get_new_parentid('league');
        $data->exercise = $this->get_mappingid('league_exercise', $data->exercise);
        $data->id_user = $this->get_mappingid('user', $data->id_user);
        
        $newitemid = $DB->insert_record('league_attempt', $data);
        $this->set_mapping('league_attempt', $oldid, $newitemid);
    }
 
    protected function after_execute() {
        // Add league related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_league', 'exuplod', null);
    }
}