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
 * Library of functions that league uses.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/league/lib.php');

/**
 * Handle league_created event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_league_created_event($league)}
 */
function league_created_handler($event) {
    debugging('league_created_handler() is deprecated, please use a call function '.
            'to trigger_league_created_event instead.', DEBUG_DEVELOPER);   
}

/**
 * Handle league_updated event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_league_updated_event($league)}
 */
function league_updated_handler($event) {
    debugging('league_updated_handler() is deprecated, please use a call function '.
            'to trigger_league_updated_event instead.', DEBUG_DEVELOPER);
}

/**
 * Handle exercise_created event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_exercise_created_event($league)}
 */
function exercise_created_handler($event) {
    debugging('exercise_created_handler() is deprecated, please use a call function '.
            'to trigger_exercise_created_event instead.', DEBUG_DEVELOPER);
}

/**
 * Handle exercise_updated event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_exercise_updated_event($league)}
 */
function exercise_updated_handler($event) {
    debugging('exercise_updated_handler() is deprecated, please use a call function '.
            'to trigger_exercise_updated_event instead.', DEBUG_DEVELOPER);
}

/**
 * Handle exercise_deleted event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_exercise_deleted_event($league)}
 */
function exercise_deleted_handler($event) {
    debugging('exercise_deleted_handler() is deprecated, please use a call function '.
            'to trigger_exercise_deleted_event instead.', DEBUG_DEVELOPER);
}

/**
 * Handle attempt_submitted event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_attempt_submitted_event($league)}
 */
function attempt_submitted_handler($event) {
    debugging('attempt_submitted_handler() is deprecated, please use a call function '.
            'to trigger_attempt_submitted_event instead.', DEBUG_DEVELOPER);
}

/**
 * Handle attempt_downloaded event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_attempt_downloaded_event($league)}
 */
function attempt_downloaded_handler($event) {
    debugging('attempt_downloaded_handler() is deprecated, please use a call function '.
            'to trigger_attempt_downloaded_event instead.', DEBUG_DEVELOPER);
}

/**
 * Handle attempt_graded event
 *
 * @param object $event The event object.
 * @deprecated since 2.6. {@link \mod_league\league::trigger_attempt_graded_event($league)}
 */
function attempt_graded_handler($event) {
    debugging('attempt_graded_handler() is deprecated, please use a call function '.
            'to trigger_attempt_graded_event instead.', DEBUG_DEVELOPER);
}
