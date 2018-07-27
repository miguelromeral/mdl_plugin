<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

class total_attempts_view implements \renderable {
 
    private $cmid = 0;
    private $attempts = null;
    private $idexer = 0;
    private $contextid = 0;
    public $name = null;
    
    public function __construct($cmid, $attempts, $idexer, $name, $contextid) {
        $this->cmid = $cmid;
        $this->idexer = $idexer;
        $this->attempts = $attempts;
        $this->name = $name;
        $this->contextid = $contextid;
    }
    
    public function attempts_exist(){
        return $this->attempts != null;
    }
    
    public function print_attempts(){

        $table = new \html_table();
        $headings = array();
        $align = array();
        array_push($headings, get_string('image', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('student', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('timemofied', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('num_attempt', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('mark', 'league'));
        array_push($align, 'center');
        $table->head = $headings;
        $table->align = $align;

        $na = array();

        foreach ($this->attempts as $d){
            $d = get_object_vars($d);
            $user = $d['firstname'] . " " . $d['lastname'];
            if(!isset($na[$user])){
                array_push($na, $user);
                $na[$user] = 0;
            }

            $na[$user] += 1;
            $na[$user ."_max"] = $na[$user];
        }

        foreach ($this->attempts as $d){
            $d = get_object_vars($d);
            $data = array();
            $data[] = \league_model::get_user_image($d['id_user'], 40);
            $user = $d['firstname'] . " " . $d['lastname'];
            $data[] = $user;
            $data[] = date("H:i:s, d (D) M Y", $d['timemodified']);


            $data[] = $na[$user];

            $data[] = (($d['mark'] == -1) ?get_string('no_mark_yet', 'league') : $d['mark']."%");

            if($d['id_file']){
                $file = \league_model::restoreURLFile($this->contextid, $d['id_file']);
                if($file){
                    $data[] = '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
                }else{
                    $data[] = get_string('cant_create_url', 'league');
                }
            }

            if($na[$user] == $na[$user ."_max"]){
                $data[] = '<form action="mark_student.php" method="get" >
                    <input type="hidden" name="id" value="'. $this->cmid .'" />
                    <input type="hidden" name="attempt" value="'. $d['id'] .'" />
                    <input type="submit" value="'. get_string('mark_student_button', 'league') .'"/>
                </form>';
            }else{
                $data[] = "";
            }

            $na[$user] -= 1;

            $table->data[] = $data;
        }

        return \html_writer::table($table);
    }

}