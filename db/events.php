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
 * League event handler definition.
 *
 * @package     mod_league
 * @category    event
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

// List of observers.
$observers = array(
    array(
        'eventname'   => '\mod_league\event\league_created',
        'callback'    => 'league_created_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\league_updated',
        'callback'    => 'league_updated_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\exercise_created',
        'callback'    => 'exercise_created_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\exercise_updated',
        'callback'    => 'exercise_updated_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\exercise_deleted',
        'callback'    => 'exercise_deleted_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\attempt_submitted',
        'callback'    => 'attempt_submitted_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\attempt_downloaded',
        'callback'    => 'attempt_downloaded_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\attempt_graded',
        'callback'    => 'attempt_graded_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    )
    
);
