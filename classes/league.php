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
}