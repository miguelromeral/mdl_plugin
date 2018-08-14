<?php


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/league/classes/model.php');
require_once($CFG->dirroot . '/mod/league/tests/lib_test.php');

/**
 * @group mod_league_test
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
    }
    
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
    
    public function test_model_get_student_name(){
        $user = $this->getDataGenerator()->create_user(array('firstname' => 'uno', 'lastname' => 'dos'));
        $expected = $user->firstname . ' ' . $user->lastname;
        $this->assertEquals($expected, $this->model->get_student_name($user->id));
    }
    
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
    
    
    public function test_model_get_context_module_id_from_league(){
        $this->assertEquals($this->cm->id, $this->model->get_context_module_id_from_league($this->league->id));
    }
 
    public function test_model_has_exercise_published_marks(){
        $eid = mod_league_lib_testcase::create_exercise($this->league->id);
        league_exercise_update_instance('name', 'statement', $this->league->id, 
                $eid, 0, 0);
        $this->assertEquals(false, $this->model->has_exercise_published_marks($eid));
        
        league_exercise_update_instance('name', 'statement', $this->league->id, 
                $eid, 0, 1);
        $this->assertEquals(true, $this->model->has_exercise_published_marks($eid));
        
    }
    
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
    
    
    public function test_model_is_last_attempt(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $at0 = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        $at1 = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        
        $this->assertEquals(false, $this->model->is_last_attempt($user->id, $exerciseid, $at0));
        $this->assertEquals(true, $this->model->is_last_attempt($user->id, $exerciseid, $at1));
    }
    
    
    public function test_model_is_league_exercise(){
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $this->assertEquals(false, $this->model->is_league_exercise($exerciseid + 1, $this->league->id));
        $this->assertEquals(true, $this->model->is_league_exercise($exerciseid, $this->league->id));
    }
    
    
    public function test_model_get_league_from_attempt(){
        $user = $this->getDataGenerator()->create_user();
        
        $exerciseid = mod_league_lib_testcase::create_exercise($this->league->id);
        
        $attemptid = mod_league_lib_testcase::create_attempt($user->id, $exerciseid);
        
        $this->assertEquals($this->league->id, $this->model->get_league_from_attempt($attemptid));
    }
    
    
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