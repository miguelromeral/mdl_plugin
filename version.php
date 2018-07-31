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
 * Version information.
 *
 * @package    mod
 * @subpackage league
 * @copyright  2018 onwards Miguel Romeral 
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2018073111;       // The current module version (Date: YYYYMMDDHH).
$plugin->requires  = 2016051900;       // Requires this Moodle version.
$plugin->component = 'mod_league';     // Full name of the plugin (used for diagnostics).
$plugin->cron      = 60;
