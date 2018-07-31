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
 * The mod_league attempt submitted event.
 *
 * @package    mod_league
 * @category   event
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_league\event;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_league attempt submitted event class.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_submitted extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_attempt';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    
    /**
     * Specifies the relationship between ID value when a restore is done.
     * @return array 
     */
    public static function get_objectid_mapping() {
        return array('db' => 'league_attempt', 'restore' => 'league_attempt');
    }
    
    /**
     * Specifies the relationship between other mapping values when a restore is done.
     * @return array 
     */
    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['exercise'] = array('db' => 'league_exercise', 'restore' => 'league_exercise');
        return $othermapped;
    }
    
    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventattemptsubmitted', 'league');
    }
    
    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' submitted an attempt "
                . "with id '$this->objectid' for exercise with id "
                . "'".$this->other['exercise']."' with course module id '$this->contextinstanceid'.";
    }
}

