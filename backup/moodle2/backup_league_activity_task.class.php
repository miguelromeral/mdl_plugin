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
 * Defines backup_league_activity_task class.
 *
 * @package     mod_league
 * @category    backup
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/mod/league/backup/moodle2/backup_league_stepslib.php');
require_once($CFG->dirroot . '/mod/league/backup/moodle2/backup_league_settingslib.php');
 
// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * Provides the steps to perform one complete backup of the League instance.
 *
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class backup_league_activity_task extends backup_activity_task {
 
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
        // League only has one structure step (the main one).
        $this->add_step(new backup_league_activity_structure_step('league_structure', 'league.xml'));
    }
 
    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links.
     * 
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
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