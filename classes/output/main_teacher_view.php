<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

class main_teacher_view implements \renderable {
 
    public $exercises = null;
    public $cmid = 0;
    public $canmark = false;
    public $alert = null;
    
    public function __construct($cmid, $exercises = null, $canmark = false, $alert = null) {
        $this->exercises = $exercises;
        $this->cmid = $cmid;
        $this->canmark = $canmark;
        $this->alert = $alert;
    }
    
    public function print_exercises(){

            $table = new \html_table();
            $headings = array();
            $align = array();
            array_push($headings, get_string('exercise', 'league'));
            array_push($align, 'center');
            array_push($headings, get_string('timemofied', 'league'));
            array_push($align, 'center');
            array_push($headings, get_string('enabled', 'league'));
            array_push($align, 'center');
            array_push($headings, get_string('published_marks', 'league'));
            array_push($align, 'center');
            $table->head = $headings;
            $table->align = $align;

            foreach ($this->exercises as $exer)
            {
                $exer = json_decode(json_encode($exer), True);
                $data = array();
                $data[] =  $exer['name'];
                $data[] =  date("H:i:s, d (D) M Y", $exer['timemodified']);
                $data[] =  ($exer['enabled'] == 0 ? 
                    '<img src="pix/no.png" width="30" height="30"/>' : 
                    '<img src="pix/yes.png" width="30" height="30"/>');
                $data[] =  ($exer['published'] == 0 ? 
                    '<img src="pix/no.png" width="30" height="30"/>' : 
                    '<img src="pix/yes.png" width="30" height="30"/>');

                $data[] = '<form action="view.php" method="post" >
                    <input type="hidden" name="id" value="'.$this->cmid.'" />
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id_exer" value="'.$exer['id'].'" />
                    <input type="hidden" name="exer_name" value="'.$exer['name'].'" />
                    <input type="hidden" name="exer_description" value="'.$exer['statement'].'" />
                    <input type="hidden" name="exer_enabled" value="'.$exer['enabled'].'" />
                    <input type="hidden" name="exer_published" value="'.$exer['published'].'" />
                    <input type="submit" value="'.get_string('del', 'league').'"/>
                </form>';

                $data[] = '<form action="add_exercise.php" method="get" >
                    <input type="hidden" name="id" value="'.$this->cmid.'" />
                    <input type="hidden" name="exercise" value="'.$exer['id'].'" />
                    <input type="submit" value="'.get_string('modify_exercise_button', 'league').'"/>
                </form>';

                $data[] = '<form action="view.php" method="post" >
                    <input type="hidden" name="id" value="'. $this->cmid .'" />
                    <input type="hidden" name="action" value="enable_disable" />
                    <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                    <input type="hidden" name="exer_name" value="'. $exer['name'] .'" />
                    <input type="hidden" name="exer_description" value="'. $exer['statement'] .'" />
                    <input type="hidden" name="exer_enabled" value="'. $exer['enabled'] .'" />
                    <input type="hidden" name="exer_published" value="'. $exer['published'] .'" />
                    <input type="submit" value="'. 
                    ($exer['enabled'] == 0 ? get_string('enable_exercise_button', 'league') : get_string('disable_exercise_button', 'league')) 
                .'"/>
                </form>';

                if($this->canmark){

                    $url= new \moodle_url('/mod/league/marking.php', array(
                            'id' => $this->cmid,
                            'exercise' => $exer['id'],
                            ));

                        
                    $data[] = '<a href="'.$url.'">'.get_string('mark_exercise', 'league')."</a>";
                    
                    /*

                    
                    $data[] = '<form action="marking.php" method="get" >
                        <input type="hidden" name="id" value="'. $this->cmid .'" />
                        <input type="hidden" name="exercise" value="'. $exer['id'] .'" />
                        <input type="submit" value="'. get_string('mark_exercise', 'league') .'"/>
                    </form>';                     */
                }

                $data[] = '<form action="view.php" method="post" >
                    <input type="hidden" name="id" value="'. $this->cmid .'" />
                    <input type="hidden" name="action" value="publish" />
                    <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                    <input type="hidden" name="exer_name" value="'. $exer['name'] .'" />
                    <input type="hidden" name="exer_description" value="'. $exer['statement'] .'" />
                    <input type="hidden" name="exer_published" value="'. $exer['published'] .'" />
                    <input type="hidden" name="exer_enabled" value="'. $exer['enabled'] .'" />
                    <input type="submit" value="'. 
                    ($exer['published'] == 0 ? get_string('publish', 'league') : get_string('unpublish', 'league')) 
                .'"/>
                </form>';

                $table->data[] = $data;
            }

            return \html_writer::table($table);


        
    }
    
    public function print_alert(){
        $out = '';
        
        if($this->alert){
            $out = '<p><center><strong>'.$this->alert.'</strong></center></p>';
        }
        
        return $out;
    }
    
    public function print_add_exercise_button(){
        
        $url= new \moodle_url('/mod/league/add_exercise.php', array(
                'id' => $this->cmid,
                'exercise' => -1,
                ));


        return '<a href="'.$url.'">'.get_string('add_exercise_button', 'league')."</a>";

        
        /*
        return '<form action="add_exercise.php" method="get">
                    <input type="hidden" name="id" value="'. $this->cmid .'" />
                    <input type="hidden" name="exercise" value="-1" />
                    <input type="submit" value="'. get_string('add_exercise_button', 'league') .'"/>
                </form>';*/
    }
}