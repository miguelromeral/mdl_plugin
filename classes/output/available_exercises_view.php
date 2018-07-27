<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

class available_exercises_view implements \renderable {
    
    public $exercises = null;
    public $cmid = 0;
    public $canupload = false;
    
    public function __construct($cmid, $exercises = null, $canupload = false) {
        
        if($exercises) {
            $this->exercises = $exercises;
        }
        
        $this->cmid = $cmid;
        $this->canupload = $canupload;
    }
    
    function print_exercises(){
        global $CFG;
        
        $table = new \html_table();
        $headings = array();
        $align = array();
        array_push($headings, get_string('exercise', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('timemofied', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('send_exercise', 'league'));
        array_push($align, 'center');

        if($this->canupload){
            array_push($headings, get_string('remaining_attempts', 'league'));
            array_push($align, 'center');
        }
        $table->head = $headings;
        $table->align = $align;

        foreach ($this->exercises as $exer)
        {
            $exer = json_decode(json_encode($exer), True);
            if($exer['enabled'] == 1){
                $data = array();
                $data[] =  $exer['name'];
                $data[] =  date("H:i:s, d (D) M Y", $exer['timemodified']);

                if($this->canupload){
                    if(!isset($exer['num'])){
                        $exer['num'] = 0;
                    }

                    if($CFG->league_max_num_attempts > $exer['num']){
                        
                        
                        $url= new \moodle_url('/mod/league/upload.php', array(
                            'id' => $this->cmid,
                            'exercise' => $exer['id'],
                            ));

                        
                        $data[] = '<a href="'.$url.'">'.get_string('upload_exercise', 'league')."</a>";
                        
                     /*   $data[] = '<form action="upload.php" method="get" >
                            <input type="hidden" name="id" value="'. $this->cmid .'" />
                            <input type="hidden" name="exercise" value="'. $exer['id'] .'" />
                            <input type="submit" value="'. get_string('upload_exercise', 'league') .'"/>
                        </form>'; */

                        $restantes = $CFG->league_max_num_attempts - $exer['num'];

                        if($restantes == 1){
                            $data[] = get_string('last_attempt', 'league');
                        }else if($restantes > 5){
                            $data[] = get_string('more_than_5_att_remaining', 'league');
                        }else{
                            $data[] = $restantes;
                            //$data[] = $restantes ." = ". $CFG->league_max_num_attempts . " - " . $exer['num'];
                        }

                    }else{
                        $data[] = get_string('max_attempts_reached', 'league');
                        $data[] = " ";
                    }
                }else{
                    $data[] = get_string('usercantupload', 'league');
                }

                $table->data[] = $data;
            }
        }

        return \html_writer::table($table);
        
    }
    
}