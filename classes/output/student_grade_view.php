<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

class student_grade_view implements \renderable {
 
    public $marks = null;
    public $cmid = 0;
    public $contextid = 0;
    public $candownload = false;
    
    public function __construct($cmid, $contextid = 0, $marks = null, $candownload = false) {
        $this->marks = $marks;
        $this->cmid = $cmid;
        $this->contextid = $contextid;
        $this->candownload = $candownload;
    }
    
    public function print_grades(){

        $table = new \html_table();
        $headings = array();
        $align = array();
        array_push($headings, get_string('exercise', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('upload_time', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('file_uploaded', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('mark', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('reviews', 'league'));
        array_push($align, 'center');
        $table->head = $headings;
        $table->align = $align;

        foreach ($this->marks as $d){
            $d = get_object_vars($d);

            if($d['enabled'] == 1 || $d['idat']){
                $this->marks = array();
                $this->marks[] = $d['name'];
                $this->marks[] = ($d['tma'] ? date("H:i:s, d (D) M Y", $d['tma']) : "");

                if($d['id_file']){
                    if($this->candownload){
                        $file = \league_model::restoreURLFile($this->contextid, $d['id_file']);
                        if($file){
                            $this->marks[] = '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
                        }else{
                            $this->marks[] = get_string('cant_create_url', 'league');
                        }
                    }else{
                        $this->marks[] = get_string('usercantdownload', 'league');
                    }
                }

                if($d['mark']){
                    if($d['mark'] == -1){
                        $this->marks[] = get_string('no_mark_yet', 'league');
                    }else{
                        if($d['published'] == 0){
                            $this->marks[] = get_string('no_mark_yet', 'league');
                        }else{
                            $this->marks[] = $d['mark']."%";
                        }
                    }
                }else{
                    $this->marks[] = "<b><i>".get_string('not_sent_yet', 'league')."</i></b>";
                    $this->marks[] = "";
                }

                if($d['mark'] == -1 || $d['published'] == 0){
                    $this->marks[] = "";
                }else{
                    $this->marks[] = $d['observations'];
                }

                $table->data[] = $this->marks;
            }
        }

        return \html_writer::table($table);

    }
    
}