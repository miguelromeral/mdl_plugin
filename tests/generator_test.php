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
 * mod_league generator test.
 *
 * @package mod_league
 * @category test
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit data generator testcase
 *
 * @package mod_league
 * @category test
 * @group mod_league
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_league_generator_testcase extends advanced_testcase {
    
    /**
     * Test the League generator.
     * 
     * @global object $DB Moodle Database.
     * @global object $SITE Moodle Site.
     */
    public function test_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('league'));

        /** @var mod_league_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_league');
        $this->assertInstanceOf('mod_league_generator', $generator);
        $this->assertEquals('league', $generator->get_modulename());

        $generator->create_instance(array('course'=>$SITE->id));
        $generator->create_instance(array('course'=>$SITE->id));
        $league = $generator->create_instance(array('course'=>$SITE->id));
        $this->assertEquals(3, $DB->count_records('league'));

        $cm = get_coursemodule_from_instance('league', $league->id);
        $this->assertEquals($league->id, $cm->instance);
        $this->assertEquals('league', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($league->cmid, $context->instanceid);
    }
}
