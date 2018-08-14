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
 * mod_league events test.
 *
 * @package mod_league
 * @category test
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/league/lib.php');
require_once($CFG->dirroot . '/mod/league/tests/lib_test.php');

/**
 * mod_choice events test.
 *
 * @package mod_league
 * @category test
 * @group mod_league
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_league_events_testcase extends advanced_testcase {
    
    /** @var league_object */
    protected $league;

    /** @var course_object */
    protected $course;

    /** @var cm_object Course module object. */
    protected $cm;

    /** @var context_object */
    protected $context;

    /**
     * Setup often used objects for the following tests.
     */
    protected function setup() {
        global $DB;

        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->league = $this->getDataGenerator()->create_module('league', array('course' => $this->course->id));
        $this->cm = $DB->get_record('course_modules', array('id' => $this->league->cmid));
        $this->context = context_module::instance($this->league->cmid);
    }
    
    /**
     * Test league_created event.
     */
    public function test_league_created() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        \mod_league\league::trigger_league_created_event($this->league, $this->context);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\league_created', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($this->league->id, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test league_updated event.
     */
    public function test_league_updated() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $updatedleague = clone $this->league;
        $this->assertNotSame($updatedleague, $this->league);
        
        \mod_league\league::trigger_league_updated_event($updatedleague, $this->context);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\league_updated', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($this->league->id, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test exercise_created event.
     */
    public function test_exercise_created() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $mod->trigger_exercise_created_event($exerciseid);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\exercise_created', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($exerciseid, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEquals($this->league->id, $events[0]->other['league']);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test exercise_updated event.
     */
    public function test_exercise_updated() {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $name = 'exercise_name';
        $statement = 'exercise_statement';
        $enabled = 0;
        $published = 0;
        $success = league_exercise_update_instance($name, $statement, $this->league->id, $exerciseid, $enabled, $published);
        
        $mod->trigger_exercise_updated_event($exerciseid);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\exercise_updated', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($exerciseid, $events[0]->objectid);
        $this->assertEquals($success, true);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEquals($this->league->id, $events[0]->other['league']);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test exercise_deleted event.
     */
    public function test_exercise_deleted() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        $name = 'exercise_name';
        $statement = 'exercise_statement';
        $exerciseid = league_exercise_add_instance($name, $statement, $this->league->id);
        $success = league_exercise_delete_instance($exerciseid);
        
        $mod->trigger_exercise_deleted_event($exerciseid);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\exercise_deleted', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($exerciseid, $events[0]->objectid);
        $this->assertEquals($success, true);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test attempt_submitted event.
     */
    public function test_attempt_submitted() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $attemptid = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        $mod->trigger_attempt_submitted_event($exerciseid, $attemptid);
        
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\attempt_submitted', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($attemptid, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEquals($exerciseid, $events[0]->other['exercise']);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test attempt_graded event.
     */
    public function test_attempt_graded() {
        $user = $this->getDataGenerator()->create_user();
        $userwhosubmit = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $attemptid = mod_league_lib_testcase::create_attempt($userwhosubmit->id, $exerciseid);
        
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        
        $mark = 100;
        $observations = 'observations';
        $success = league_attempt_update_instance($this->league->id, $attemptid, $mark, $observations, $exerciseid);
        
        $mod->trigger_attempt_graded_event($attemptid, $userwhosubmit->id, $exerciseid, $mark);
        
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\attempt_graded', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($success, true);
        $this->assertEquals($attemptid, $events[0]->objectid);
        $this->assertEquals($userwhosubmit->id, $events[0]->relateduserid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEquals($exerciseid, $events[0]->other['exercise']);
        $this->assertEquals($mark, $events[0]->other['mark']);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    /**
     * Test attempt_downloaded event.
     */
    public function test_attempt_downloaded() {
        $user = $this->getDataGenerator()->create_user();
        $userwhosubmit = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $attemptid = mod_league_lib_testcase::create_attempt($userwhosubmit->id, $exerciseid);
        \mod_league\league::trigger_attempt_downloaded_event($attemptid, $userwhosubmit->id, $this->league->id, $exerciseid, $this->context);
        
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\attempt_downloaded', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($attemptid, $events[0]->objectid);
        $this->assertEquals($userwhosubmit->id, $events[0]->relateduserid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEquals($exerciseid, $events[0]->other['exercise']);
        $this->assertEquals($this->league->id, $events[0]->other['league']);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
}