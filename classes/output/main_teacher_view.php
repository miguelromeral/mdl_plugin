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
 * The renderer of every exercise on the league for the teacher.
 *
 * @package    mod_league
 * @category   output
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

/**
 * The renderer class to show all exercises for teacher and operations
 * with these.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main_teacher_view implements \renderable {
 
    /** @var object Array with all exercise data. */
    public $exercises = null;
    
    /** @var int Course Module ID. */
    public $cmid = 0;
    
    /** @var bool Capability if the user can mark students. */
    public $canmark = false;
    
    /** @var string Alert due to an action of the user. */
    public $alert = null;
    
    /**
     * Class constructor.
     * 
     * @param int $cmid Course Module ID.
     * @param object $exercises Array with all exercise data.
     * @param bool $canmark Capability if the user can mark students.
     * @param string $alert Alert due to an action of the user.
     */
    public function __construct($cmid, $exercises = null, $canmark = false, $alert = null) {
        $this->exercises = $exercises;
        $this->cmid = $cmid;
        $this->canmark = $canmark;
        $this->alert = $alert;
    }
    
    /**
     * Print all exercises and buttons to allow teachers do actions
     * with these exercises (i.e: enable an exercise, mark the attempts,
     * etc.).
     * 
     * @return string HTML output table.
     */
    public function print_exercises(){

        // Create table and set the parameters.
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

        // For every exercise, make a row to the table.
        foreach ($this->exercises as $exer)
        {
            $exer = json_decode(json_encode($exer), True);
            $data = array();
            
            // Modify an exercise.
            $url= new \moodle_url('/mod/league/add_exercise.php', array(
                    'id' => $this->cmid,
                    'exercise' => $exer['id'],
                    ));

            $data[] = '<a href="'.$url.'" title="'. get_string('modify_exercise_title', 'league') .'">'.$exer['name']."</a>";
            
            // Time of modification.
            $data[] =  date("H:i:s, d (D) M Y", $exer['timemodified']);
            
            // Enable / Disable an exercise.
            $image = null;
            $title = null;
            if($exer['enabled'] == 0){
                $image = 'pix/no.png';
                $title = get_string('enable_exercise_button', 'league');
            }else{
                $image = 'pix/yes.png';
                $title = get_string('disable_exercise_button', 'league');
            }
            $data[] = '<form action="view.php" method="post" >
                <input type="hidden" name="id" value="'. $this->cmid .'" />
                <input type="hidden" name="action" value="enable_disable" />
                <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                <input type="image" title="'. $title .'" src="'. $image .'" width="30" height="30">
            </form>';
            
            // Publish / Unpublish students grades to that exercise.
            $image = null;
            $title = null;
            if($exer['published'] == 0){
                $image = 'pix/no.png';
                $title = get_string('publish', 'league');
            }else{
                $image = 'pix/yes.png';
                $title = get_string('unpublish', 'league');
            }
            $data[] = '<form action="view.php" method="post" >
                <input type="hidden" name="id" value="'. $this->cmid .'" />
                <input type="hidden" name="action" value="publish" />
                <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                <input type="image" title="'. $title .'" src="'. $image .'" width="30" height="30">
            </form>';
            
            // If the user can mark students, print a link to redirect there.
            if($this->canmark){
                $url= new \moodle_url('/mod/league/marking.php', array(
                        'id' => $this->cmid,
                        'exercise' => $exer['id'],
                        ));

                $data[] = '<a href="'.$url.'">'.get_string('to_grade', 'league')."</a>";
            }

            $table->data[] = $data;
        }

        return \html_writer::table($table);
    }
    
    /**
     * Return an alert in HTML if the user did an action.
     * 
     * @return string
     */
    public function print_alert(){
        $out = '';
        
        if($this->alert){
            $out = '<p><center><strong>'.$this->alert.'</strong></center></p>';
        }
        
        return $out;
    }
    
    /**
     * Print a link to redirect the user to the page to add exercises.
     * 
     * @return string HTML string link.
     */
    public function print_add_exercise_button(){
        $url= new \moodle_url('/mod/league/add_exercise.php', array(
                'id' => $this->cmid,
                'exercise' => -1,
                ));


        return '<a href="'.$url.'">'.get_string('add_exercise', 'league')."</a>";
    }
}