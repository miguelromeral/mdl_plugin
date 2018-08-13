<?php


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/league/lib.php');
require_once($CFG->dirroot . '/mod/league/tests/lib_test.php');

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
    
    public function test_league_created() {
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Redirect event.
        $sink = $this->redirectEvents();
        
        \mod_league\league::trigger_league_created_event($this->league, $this->context);
        // Recover events.
        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\league_created', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($this->league->id, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    public function test_league_updated() {
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Redirect event.
        $sink = $this->redirectEvents();
        
        $updatedleague = clone $this->league;
        $this->assertNotSame($updatedleague, $this->league);
        
        \mod_league\league::trigger_league_updated_event($updatedleague, $this->context);
        // Recover events.
        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\league_updated', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($this->league->id, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    public function test_exercise_created() {
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Redirect event.
        $sink = $this->redirectEvents();
        
        // Create an instance of league (to throw the event).
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        // Create the exercise and throw the event.
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $mod->trigger_exercise_created_event($exerciseid);
        // Recover events.
        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\exercise_created', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($exerciseid, $events[0]->objectid);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEquals($this->league->id, $events[0]->other['league']);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
    
    
    public function test_exercise_updated() {
        global $DB;
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Redirect event.
        $sink = $this->redirectEvents();
        
        // Create an instance of league (to throw the event).
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        // Create the exercise and throw the event (WHITOUT NO EVENT TRIGGERED).
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        // Update the exercise with ID.
        $name = 'exercise_name';
        $statement = 'exercise_statement';
        $enabled = 0;
        $published = 0;
        $success = league_exercise_update_instance($name, $statement, $this->league->id, $exerciseid, $enabled, $published);
        
        $mod->trigger_exercise_updated_event($exerciseid);
        // Recover events.
        $events = $sink->get_events();

        // Data checking.
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
    
    
    public function test_exercise_deleted() {
        global $DB;
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Redirect event.
        $sink = $this->redirectEvents();
        
        // Create an instance of league (to throw the event).
        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        $mod = new mod_league\league($cminfo, $this->context, $this->league);
        // Create the exercise and throw the event (WHITOUT NO EVENT TRIGGERED).
        $name = 'exercise_name';
        $statement = 'exercise_statement';
        $exerciseid = league_exercise_add_instance($name, $statement, $this->league->id);
        // Update the exercise with ID.
        $success = league_exercise_delete_instance($exerciseid);
        
        $mod->trigger_exercise_deleted_event($exerciseid);
        // Recover events.
        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_league\event\exercise_deleted', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals($exerciseid, $events[0]->objectid);
        $this->assertEquals($success, true);
        $this->assertEquals(context_module::instance($this->league->cmid), $events[0]->get_context());
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }
}