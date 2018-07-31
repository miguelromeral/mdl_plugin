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
 * Defines the League class
 *
 * @package     mod_league
 * @copyright   2018 Miguel Romeral
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_league;
 
require_once($CFG->dirroot . '/mod/league/lib.php');

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * A full controller on League.
 *
 * Class that is used as a Controller (Like an MVC pattern). This class can check
 * capabilities from an user and can handle determinate functions that are userfull
 * to make the plugin running.
 *
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class league {
    
    /** @var \cm_info The info of Course Module. */
    private $cm;
    
    /** @var object The context of the module. */
    private $context;
    
    /** @var object The league instance. */
    private $league = null;
 
    function __construct(\cm_info $cm, $context_module, $league) {
        $this->cm = $cm;
        $this->context = $context_module;
        $this->league = $league;
    }
    
    
    
    // Begin to check capabilities from users.
    // This is an idea viewed on 
    // {@link https://docs.moodle.org/dev/NEWMODULE_Documentation#Check_the_capability_of_a_user}.
    
    /**
     * Check if an user can view the module.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function userview($userid = null){
        return (has_capability('mod/league:view', $this->context, $userid));
    }
    
    /**
     * Check if an user can manage the module.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function usermanageexercises($userid = null){
        return (has_capability('mod/league:manageexercises', $this->context, $userid));
    }
    
    /**
     * Check if an user can see a FULL qualy.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function userseequaly($userid = null){
        return (has_capability('mod/league:seequaly', $this->context, $userid));
    }
    
    /**
     * Check if an user can see a RESTRICTED qualy.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function userseerestrictedqualy($userid = null){
        return (has_capability('mod/league:seerestrictedqualy', $this->context, $userid));
    }
    
    /**
     * Check if an user can download files.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function userdownloadfiles($userid = null){
        return (has_capability('mod/league:downloadfiles', $this->context, $userid));
    }
    
    /**
     * Check if an user can upload files.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function useruploadfiles($userid = null){
        return (has_capability('mod/league:uploadfiles', $this->context, $userid));
    }
    
    /**
     * Check if an user can mark students attempt.
     * @param int $userid The user ID (from $USER->id).
     * @return bool
     */
    public function usermarkstudents($userid = null){
        return (has_capability('mod/league:markstudents', $this->context, $userid));
    }
    
    public static function trigger_league_created_event($league) {

        $params = array(
            'objectid' => $league->id,
            'context' => \context_module::instance($league->coursemodule)
        );

        $event = \mod_league\event\league_created::create($params);
        $event->trigger();
    }

    public static function trigger_league_updated_event($league){

        $params = array(
            'objectid' => $league->id,
            'context' => \context_module::instance($league->coursemodule)
        );

        $event = \mod_league\event\league_updated::create($params);
        $event->trigger();
    }

    public function trigger_exercise_created_event($id) {

        $params = array(
            'objectid' => $id,
            'other' => array('league' => $this->league->id),
            'context' => $this->context
        );

        $event = \mod_league\event\exercise_created::create($params);
        $event->trigger();
    }

    public function trigger_exercise_deleted_event($idexer){
        $params = array(
            'objectid' => $idexer,
            'context' => $this->context
        );

        $event = \mod_league\event\exercise_deleted::create($params);

        $event->trigger();
    }

    public function trigger_exercise_updated_event($idexer){
        $params = array(
            'objectid' => $idexer,
            'other' => array('league' => $this->league->id),
            'context' => $this->context
        );

        $event = \mod_league\event\exercise_updated::create($params);
        $event->trigger();
    }

    public static function trigger_attempt_downloaded_event($idat, $iduser, $idleague, $idexer, $context){
        $params = array(
            'objectid' => $idat,
            'relateduserid' => $iduser,
            'other' => array('league' => $idleague,
                                'exercise' => $idexer),
            'context' => $context
        );

        $event = \mod_league\event\attempt_downloaded::create($params);
        $event->trigger();
    }

    public function trigger_attempt_submitted_event($exercise, $id) {

        $params = array(
            'objectid' => $id,
            'other' => array('exercise' => $exercise),
            'context' => $this->context
        );

        $event = \mod_league\event\attempt_submitted::create($params);
        $event->trigger();
    }

    
    public function trigger_attempt_graded_event($attemptid, $user, $exercise, $mark) {

        $params = array(
            'objectid' => $attemptid,
            'relateduserid' => $user,
            'other' => array('exercise' => $exercise,
                            'mark' => $mark),
            'context' => $this->context
        );

        $event = \mod_league\event\attempt_graded::create($params);
        $event->trigger();
    }
}