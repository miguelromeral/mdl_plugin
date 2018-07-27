<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

class qualy_view implements \renderable {
 
    public $role = 'student';
    public $cmid = 0;
    public $userid = -1;
    public $qualy = null;
    public $title = null;
    
    public function __construct($title, $cmid, $qualy, $userid, $rol = 'student') {
        $this->role = $rol;
        $this->cmid = $cmid;
        $this->userid = $userid;
        $this->qualy = $qualy;
        $this->title = $title;
    }
    
    public function print_qualy(){
        $pos = 1;

        $table = new \html_table();
        $headings = array();
        $align = array();
        array_push($headings, get_string('q_pos', 'league'));
        array_push($align, 'center');

        if($this->role == 'student'){
            array_push($headings, get_string('q_name_hashed', 'league'));
            array_push($align, 'center');
        }else{
            array_push($headings, get_string('image', 'league'));
            array_push($align, 'center');
            array_push($headings, get_string('q_name', 'league'));
            array_push($align, 'center');
        }

        if($this->role == 'teacher'){ 
            array_push($headings, get_string('q_user', 'league'));
            array_push($align, 'center');
            array_push($headings, get_string('q_id', 'league'));
            array_push($align, 'center');
        }

        array_push($headings, get_string('q_total_exercises', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('q_exercises_uploaded', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('q_total_mark', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('q_percentage', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('q_notes', 'league'));
        array_push($align, 'center');

        $table->head = $headings;
        $table->align = $align;

        foreach ($this->qualy as $r){
            $data = array();
            $data[] = $pos;

            if($this->role == 'teacher'){ 
                $data[] = \league_model::get_user_image($r['uid'], 50);
                $data[] = $r['name'];
                $data[] = $r['uname'];
            } else if($this->role == 'student'){ 
                if($r['uid'] == $this->userid){
                    $data[] = $r['name'];
                } else {
                    $data[] = md5($r['name']." - ".$r['uname']);
                }
            } 

            if($this->role == 'teacher'){
                $data[] = $r['uid'];
            }

            $data[] = $r['totalexer'];
            $data[] = $r['exeruplo'];
            $data[] = $r['totalmark'];
            $data[] = ($r['totalexer'] > 0 ? number_format(($r['totalmark'] / ($r['totalexer'] * 100)) * 100, 2, ',', ' ') . ' %' : 'NaN');
            $data[] = $r['notes'];

            $table->data[] = $data;
            if($r['notes'] !== get_string('total_draw','league')){
                $pos += 1;
            }
        }

        return \html_writer::table($table);
    }
    
    public function print_qualy_explanation($output){
        $out = '';
        
        // Explanation about the heading columns.
        $out  .= $output->container(get_string('q_pos','league').': '.
                get_string('q_pos_des','league'));
        $out  .= $output->container(get_string('q_total_exercises','league').': '.
                get_string('q_total_exercises_des','league'));
        $out  .= $output->container(get_string('q_exercises_uploaded','league').': '.
                get_string('q_exercises_uploaded_des','league'));
        $out  .= $output->container(get_string('q_total_mark','league').': '.
                get_string('q_total_mark_des','league'));
        $out  .= $output->container(get_string('q_percentage','league').': '.
                get_string('q_percentage_des','league'));
        $out  .= $output->container(get_string('q_notes','league').': '.
                get_string('q_notes_des','league'));
        
        return $out;
    }
}