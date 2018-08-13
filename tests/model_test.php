<?php


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/league/classes/model.php');
require_once($CFG->dirroot . '/mod/league/tests/lib_test.php');

class mod_league_model_testcase extends advanced_testcase {
    /** @var league_object */
    protected $league;

    /** @var course_object */
    protected $course;

    /** @var cm_object Course module object. */
    protected $cm;

    /** @var context_object */
    protected $context;

    protected $model;
    
    /**
     * Setup often used objects for the following tests.
     */
    protected function setup() {
        global $DB;

        $this->resetAfterTest();

        $this->model = new league_model;
        
        $this->course = $this->getDataGenerator()->create_course();
        $this->league = $this->getDataGenerator()->create_module('league', array('course' => $this->course->id));
        $this->cm = $DB->get_record('course_modules', array('id' => $this->league->cmid));
        $this->context = context_module::instance($this->league->cmid);
    }
/*
    public function test_model_get_user_by_id(){
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Data checking.
        $this->assertEquals($user, $this->model->get_user_by_id($user->id));
    }

    public function test_model_get_exercises_from_id(){
        $id0 = mod_league_lib_testcase::create_exercise($this->league->id);
        $id1 = mod_league_lib_testcase::create_exercise($this->league->id);
        $id2 = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $exercises = $this->model->get_exercises_from_id($this->league->id);
        
        // Data checking.
        $this->assertCount(3, $exercises);
        $this->assertEquals($id0, array_shift($exercises)->id);
        $this->assertEquals($id1, array_shift($exercises)->id);
        $this->assertEquals($id2, array_shift($exercises)->id);
    }

    public function test_model_get_league_from_course(){
        $league = $this->model->get_league_from_course($this->course->id);
        $league = array_shift($league);
        $league->cmid = $this->cm->id;
        $this->assertEquals($this->league, $league);
    }
    
    public function test_model_get_exercises_from_id_by_user(){
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        
        $eid0 = mod_league_lib_testcase::create_exercise($this->league->id);
        $eid1 = mod_league_lib_testcase::create_exercise($this->league->id);
        $eids = array($eid0, $eid1);
        
        mod_league_lib_testcase::create_attempt($user->id, $eid0);
        mod_league_lib_testcase::create_attempt($user->id, $eid1);
        
        $exercises = $this->model->get_exercises_from_id_by_user($this->league->id, $user->id);

        $this->assertCount(2, $exercises);
        for($i = 0; $i < 2; $i++){
            $eid = array_shift($eids);
            $exercise = array_shift($exercises);
            
            $this->assertEquals($eid, $exercise->id);
            $this->assertEquals(1, $exercise->num);
            $this->assertEquals($this->league->id, $exercise->league);
        }
    }*/
    
}