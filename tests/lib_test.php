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
 * mod_league lib functions for tests.
 *
 * @package mod_league
 * @category test
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/league/lib.php');

/**
 * mod_league lib functions for tests.
 *
 * @package mod_league
 * @category test
 * @group mod_league
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_league_lib_testcase extends externallib_advanced_testcase {

    /**
     * Create an exercise and return its ID.
     * 
     * @param int $leagueid League ID.
     * @return int Created Exercise ID.
     */
    public static function create_exercise($leagueid){
        $name = 'exercise_name';
        $statement = 'exercise_statement';
        $exerciseid = league_exercise_add_instance($name, $statement, $leagueid);
        return $exerciseid;
    }
    
    /**
     * Create an attempt and return its ID.
     * 
     * @param int $userid User ID.
     * @param int $exerciseid Exercise ID.
     * @return int Created Attempt ID.
     */
    public static function create_attempt($userid, $exerciseid){
        $name = 'attempt_name';
        $itemid = 123456789123;
        $attemptid = league_attempt_add_instance($userid, $exerciseid, $itemid, $name);
        return $attemptid;
    }
    
}