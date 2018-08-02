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
 * Main page to set up the plugin.
 *
 * @package   mod_league
 * @category  admin
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die;

// If Moodle has created the full Admin tree:
if ($ADMIN->fulltree) {
    
    require_once($CFG->dirroot.'/mod/league/lib.php');

    // Settings to specify maximum attempts number (1 to 10).
    $options = array();
    $options[1] = get_string('only_one_attempt', 'league');
    for($i = 2; $i <= 10; $i++){
        $options[$i] = $i;
    }
    
    // Add the maximum attempts number to settings.
    $settings->add(new admin_setting_configselect('league_max_num_attempts',
            get_string('max_num_attempts', 'league'),
            get_string('max_num_attempts_desc', 'league'),
            5, $options));
    
}

