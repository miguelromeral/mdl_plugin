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
 * Capability definitions for the league module.
 *
 * @package    mod_league
 * @category   access
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    // Ability to add an instance of a league.
    'mod/league:addinstance' => array(
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    
    // Ability to view the league (then, the user can do 
    // appropiate tasks, like manage exercises, upload 
    // answers, etc.). Every user who interact with the 
    // league MUST HAVE this capability on.
    'mod/league:view' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
            )
    ),
    
    // Ability to manage exercises (create, modify, delete, enable,
    // publish, etc.). This capability ONLY MUST BE for teachers
    // and admins (and similar roles).
    'mod/league:manageexercises' => array(
        'riskbitmask' => RISK_DATALOSS | RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
            )
    ),
    
    // Ability to see the FULL QUALY, it means with no hidden info.
    // Teachers and similar roles MUST HAVE this capability to see
    // the current qualy with all necessary data.
    'mod/league:seequaly' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
            )
    ),
    
    // Ability to see a RESTRICTED QUALY. In this one, the users data
    // is HIDDEN (their names) and only is printed the users data who 
    // is looking at.
    'mod/league:seerestrictedqualy' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW
            )
    ),
    
    // Ability to download files that students have upload before.
    // The only files the module is handling is the task belongs
    // to an exercise. This capability allows students role users
    // download their own files uploaded and the teachers role users
    // to download that files in order to mark the student work.
    'mod/league:downloadfiles' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'student' => CAP_ALLOW
            )
    ),
    
    // Ability to upload files for studentds, who will upload
    // the answers to the exercises enabled by the teachers.
    'mod/league:uploadfiles' => array(
        'riskbitmask' => RISK_XSS | RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW
            )
    ),
    
    // Ability to mark the students attempts.
    'mod/league:markstudents' => array(
        'riskbitmask' => RISK_PERSONAL | RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
            )
    )
    
);

