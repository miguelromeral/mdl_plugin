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
 * The renderer of available exercises enabled by the teacher.
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
 * The renderer class to show enabled exercises to students.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class available_exercises_view implements \renderable {
    
    /** @var object List of all exercises enabled. */
    public $exercises = null;
    
    /** @var int Course Module ID. */
    public $cmid = 0;
    
    /** @var bool Check of capability if user can upload exercises. */
    public $canupload = false;
    
    /**
     * Class constructor.
     * 
     * @param int $cmid Course Module ID.
     * @param object $exercises List of all exercises enabled (if there are).
     * @param bool $canupload Capability if user can upload exercises.
     */
    public function __construct($cmid, $exercises = null, $canupload = false) {
        // Sometimes there are no exercises. The renderer handle it.
        if($exercises) {
            $this->exercises = $exercises;
        }
        
        $this->cmid = $cmid;
        $this->canupload = $canupload;
    }
    
    /**
     * Print a HTML table with all the exercises enabled.
     * 
     * @global object $CFG Global Moodle configuration.
     * @return string HTML table.
     */
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

        // We print the "marking link" only if the user can do it.
        if($this->canupload){
            array_push($headings, get_string('remaining_attempts', 'league'));
            array_push($align, 'center');
        }
        
        $table->head = $headings;
        $table->align = $align;

        // Create a row for each exercise.
        foreach ($this->exercises as $exercise)
        {
            $exercise = json_decode(json_encode($exercise), True);
            
            // Only print the enabled ones.
            if($exercise['enabled'] == 1){
                $data = array();
                $data[] =  $exercise['name'];
                $data[] =  date("H:i:s, d (D) M Y", $exercise['timemodified']);

                if($this->canupload){
                    
                    // Check attempts sent by the user.
                    if(!isset($exercise['num'])){
                        $exercise['num'] = 0;
                    }

                    // If the user has not sent limit attempt, print the link to do it.
                    if($CFG->league_max_num_attempts > $exercise['num']){
                        
                        
                        $url= new \moodle_url('/mod/league/upload.php', array(
                            'id' => $this->cmid,
                            'exercise' => $exercise['id'],
                            ));

                        
                        $data[] = '<a href="'.$url.'">'.get_string('upload_exercise', 'league')."</a>";
                        
                        $remaining = $CFG->league_max_num_attempts - $exercise['num'];

                        // Print different text in function of remaining attempts.
                        if($remaining == 1){
                            $data[] = get_string('last_attempt', 'league');
                        }else if($remaining > 5){
                            $data[] = get_string('more_than_5_att_remaining', 'league');
                        }else{
                            $data[] = $remaining;
                        }

                    }else{
                        // Maximum attempts reached.
                        $data[] = get_string('max_attempts_reached', 'league');
                        $data[] = " ";
                    }
                    
                }else{
                    // User can not upload (don't have that capability).
                    $data[] = get_string('usercantupload', 'league');
                }

                $table->data[] = $data;
            }
        }

        // Return the HTML string.
        return \html_writer::table($table);
        
    }
    
}