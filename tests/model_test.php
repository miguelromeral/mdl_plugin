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
 * mod_league model test.
 *
 * @package mod_league
 * @category test
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/league/classes/model.php');
require_once($CFG->dirroot . '/mod/league/tests/lib_test.php');

/**
 * mod_league model test.
 *
 * @package mod_league
 * @category test
 * @group mod_league
 * @copyright 2018 Miguel Romeral
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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

    /**
     * Test model function get_user_by_id.
     */
    public function test_model_get_user_by_id(){
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->assertEquals($user, $this->model->get_user_by_id($user->id));
    }

    /**
     * Test model function get_exercises_from_id.
     */
    public function test_model_get_exercises_from_id(){
        $id0 = mod_league_lib_testcase::create_exercise($this->league->id);
        $id1 = mod_league_lib_testcase::create_exercise($this->league->id);
        $id2 = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $exercises = $this->model->get_exercises_from_id($this->league->id);
        
        $this->assertCount(3, $exercises);
        $this->assertEquals($id0, array_shift($exercises)->id);
        $this->assertEquals($id1, array_shift($exercises)->id);
        $this->assertEquals($id2, array_shift($exercises)->id);
    }

    /**
     * Test model function get_league_from_course.
     */
    public function test_model_get_league_from_course(){
        $league = $this->model->get_league_from_course($this->course->id);
        $league = array_shift($league);
        $league->cmid = $this->cm->id;
        $this->assertEquals($this->league, $league);
    }
    
    
    /**
     * Test model function get_exercises_from_id_by_user.
     */
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
    }
    
    /**
     * Test model function get_attempts_by_exercise.
     */
    public function test_model_get_attempts_by_exercise(){
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        
        $eid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $aid0 = mod_league_lib_testcase::create_attempt($user->id, $eid);
        $aid1 = mod_league_lib_testcase::create_attempt($user->id, $eid);
        
        $attempts = $this->model->get_attempts_by_exercise($eid);
        
        $this->assertCount(2, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals($aid0, $attempt->id);
        $this->assertEquals($eid, $attempt->exercise);
        $this->assertEquals($user->id, $attempt->user);
        $attempt = array_shift($attempts);
        $this->assertEquals($aid1, $attempt->id);
        $this->assertEquals($eid, $attempt->exercise);
        $this->assertEquals($user->id, $attempt->user);
    }
    
    /**
     * Test model function get_student_name.
     */
    public function test_model_get_student_name(){
        $user = $this->getDataGenerator()->create_user(array('firstname' => 'uno', 'lastname' => 'dos'));
        $expected = $user->firstname . ' ' . $user->lastname;
        $this->assertEquals($expected, $this->model->get_student_name($user->id));
    }
    
    /**
     * Test model function get_student_marks.
     */
    public function test_model_get_student_marks(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $at0 = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        $at1 = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        
        $mark = 100;
        league_attempt_update_instance($this->league->id, $at1, $mark, '', $exerciseid);
        
        $attempts = $this->model->get_student_marks($this->league->id, $user->id);
        
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertNotEquals($at1, $at0);
        $this->assertEquals($at1, $attempt->idat);
        $this->assertEquals($mark, $attempt->mark);   
    }
    
    /**
     * Test model function get_context_module_id_from_league.
     */
    public function test_model_get_context_module_id_from_league(){
        $this->assertEquals($this->cm->id, $this->model->get_context_module_id_from_league($this->league->id));
    }
 
    /**
     * Test model function has_exercise_published_marks.
     */
    public function test_model_has_exercise_published_marks(){
        $eid = mod_league_lib_testcase::create_exercise($this->league->id);
        league_exercise_update_instance('name', 'statement', $this->league->id, 
                $eid, 0, 0);
        $this->assertEquals(false, $this->model->has_exercise_published_marks($eid));
        
        league_exercise_update_instance('name', 'statement', $this->league->id, 
                $eid, 0, 1);
        $this->assertEquals(true, $this->model->has_exercise_published_marks($eid));
        
    }
    
    /**
     * Test model function get_student_individual_marks_to_teacher.
     */
    public function test_model_get_student_individual_marks_to_teacher(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $attemptid = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        
        $mark = 100;
        league_attempt_update_instance($this->league->id, $attemptid, $mark, '', $exerciseid);
        
        $attempts = $this->model->get_student_individual_marks_to_teacher($user->id, $this->league->id);
        
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals($exerciseid, $attempt->exercise);  
        $this->assertEquals($mark, $attempt->mark);  
    }
    
    /**
     * Test model function is_last_attempt.
     */
    public function test_model_is_last_attempt(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $at0 = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        $at1 = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        
        $this->assertEquals(false, $this->model->is_last_attempt($user->id, $exerciseid, $at0));
        $this->assertEquals(true, $this->model->is_last_attempt($user->id, $exerciseid, $at1));
    }
    
    /**
     * Test model function is_league_exercise.
     */
    public function test_model_is_league_exercise(){
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $this->assertEquals(false, $this->model->is_league_exercise($exerciseid + 1, $this->league->id));
        $this->assertEquals(true, $this->model->is_league_exercise($exerciseid, $this->league->id));
    }
    
    /**
     * Test model function get_league_from_attempt.
     */
    public function test_model_get_league_from_attempt(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $attemptid = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        
        $this->assertEquals($this->league->id, $this->model->get_league_from_attempt($attemptid));
    }
    
    /**
     * Test model function get_data_from_attempt.
     */
    public function test_model_get_data_from_attempt(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $name = 'attempt_name';
        $mark = 100;
        $observations = 'observations';
        $itemid = 123456789123;
        $attemptid = league_attempt_add_instance($user->id, $exerciseid, $itemid, $name);
        league_attempt_update_instance($this->league->id, $attemptid, $mark, $observations, $exerciseid);
        
        $this->assertEquals($name, $this->model->get_data_from_attempt($attemptid, 'name'));
        $this->assertEquals($itemid, $this->model->get_data_from_attempt($attemptid, 'itemid'));
        $this->assertEquals($observations, $this->model->get_data_from_attempt($attemptid, 'observations'));
        $this->assertEquals($mark, $this->model->get_data_from_attempt($attemptid, 'mark'));
        $this->assertEquals($exerciseid, $this->model->get_data_from_attempt($attemptid, 'exercise'));
        $this->assertEquals($attemptid, $this->model->get_data_from_attempt($attemptid, 'id'));
        $this->assertEquals($user->id, $this->model->get_data_from_attempt($attemptid, 'user'));
    }
    
    /**
     * Test model function get_attempt_data_by_itemid.
     */
    public function test_model_get_attempt_data_by_itemid(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $name = 'attempt_name';
        $itemid = 123456789123;
        $attemptid = league_attempt_add_instance($user->id, $exerciseid, $itemid, $name);
        
        $attempt = $this->model->get_attempt_data_by_itemid($itemid);
        $this->assertEquals($attemptid, $attempt->id);
        $this->assertEquals($user->id, $attempt->user);
        $this->assertEquals($exerciseid, $attempt->exercise);
    }
    
    /**
     * Test model function get_qualy_data.
     */
    public function test_model_get_qualy_data(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        $name = 'attempt_name';
        $mark = 100;
        $observations = 'observations';
        $itemid = 123456789123;
        $attemptid = league_attempt_add_instance($user->id, $exerciseid, $itemid, $name);
        league_attempt_update_instance($this->league->id, $attemptid, $mark, $observations, $exerciseid);
        
        $data = $this->model->get_qualy_data($this->league->id, $user->id, 'teacher');
        
        $this->assertCount(1, $data);
        $data = array_shift($data);
        
        $this->assertEquals(1, $data->te);
        $this->assertEquals(1, $data->eu);
        $this->assertEquals($mark, $data->acum);
        $this->assertEquals(0, $data->sc);
    }
    
}