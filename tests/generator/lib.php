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
 * mod_league data generator.
 *
 * @package mod_league
 * @category test
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_league data generator.
 *
 * @package mod_league
 * @category test
 * @group mod_league
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_league_generator extends testing_module_generator {

    /**
     * Create an instance of League for tests.
     * 
     * @global object $CFG Global Moodle Configuration.
     * @param object $record Record with data to be created.
     * @param object $options Options to create the instance.
     * @return object Instance of League created.
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;

        $record = (object)(array)$record;

        $defaultleaguesettings = array(
            'method' => 1
        );

        foreach ($defaultleaguesettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }
}
