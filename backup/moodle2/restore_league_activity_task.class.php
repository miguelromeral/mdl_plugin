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

require_once($CFG->dirroot . '/mod/league/backup/moodle2/restore_league_stepslib.php'); // Because it exists (must)
 
// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * League restore task that provides all the settings and steps to perform one
 * complete restore of the activity.
 * 
 * @copyright   2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_league_activity_task extends restore_activity_task {
 
    /**
     * Define particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }
 
    /**
     * Define particular steps this activity can have.
     */
    protected function define_my_steps() {
        // League only has one structure step.
        $this->add_step(new restore_league_activity_structure_step('league_structure', 'league.xml'));
    }
 
    /**
     * Define the contents in the activity that must be
     * processed by the link decoder.
     */
    static public function define_decode_contents() {
        $contents = array();
 
        $contents[] = new restore_decode_content('league', array('intro'), 'league');
 
        return $contents;
    }
 
    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
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
     * of {@link restore_log_rule} objects.
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
 
}