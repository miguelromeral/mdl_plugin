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
 * The renderer of the qualy.
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
//require_once($CFG->dirroot.'/mod/league/classes/model.php');

/**
 * The renderer class to show the qualy.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qualy_view implements \renderable {
 
    /** @var string Student role type. */
    public $role = 'student';
    
    /** @var int Course Module ID. */
    public $cmid = 0;
    
    /** @var int User ID. */
    public $userid = -1;
    
    /** @var object Array with the qualy data sorted. */
    public $qualy = null;
    
    /** @var string Header title. */
    public $title = null;
    
    /**
     * Class constructor.
     * 
     * @param string $title Header title. 
     * @param int $cmid Course Module ID. 
     * @param object $qualy Array with the qualy data sorted. 
     * @param int $userid User ID. 
     * @param string $rol Student role type. 
     */
    public function __construct($title, $cmid, $qualy, $userid, $rol = 'student') {
        $this->role = $rol;
        $this->cmid = $cmid;
        $this->userid = $userid;
        $this->qualy = $qualy;
        $this->title = $title;
    }
    
    /**
     * Print a table with the qualy data. In function of the
     * user role, some data will be hiden to the students and
     * it will be more verbose to the teachers.
     * 
     * @global type $OUTPUT Usefull to print user image.
     * @return string HTML output string.
     */
    public function print_qualy(){
        global $OUTPUT;
        $position = 1;

        // Create the table with all parameters.
        $table = new \html_table();
        $headings = array();
        $align = array();
        array_push($headings, get_string('q_pos', 'league'));
        array_push($align, 'center');

        // Students will see hashed user names (DATA PRIVACY).
        if($this->role == 'student'){
            array_push($headings, get_string('q_name_hashed', 'league'));
            array_push($align, 'center');
        }else{
            // Teachers also will see their profile pictures.
            array_push($headings, get_string('image', 'league'));
            array_push($align, 'center');
            array_push($headings, get_string('q_name', 'league'));
            array_push($align, 'center');
        }

        // Teachers can see the user ID (for events tracking).
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

        // Each row has an user data.
        foreach ($this->qualy as $row){
            $data = array();
            $data[] = '<font size="4"><b><i>'.$position.'</i></b></font>';

            // Image and name for teachers.
            // Hashed name for students, but their own user.
            if($this->role == 'teacher'){ 
                $data[] = $OUTPUT->render($row['picture']);
                $data[] = $row['name'];
                $data[] = $row['uname'];
            } else if($this->role == 'student'){ 
                if($row['uid'] == $this->userid){
                    $data[] = $row['name'];
                } else {
                    $data[] = sha1($row['name']." - ".$row['uname']);
                }
            } 

            // User ID for teachers.
            if($this->role == 'teacher'){
                $data[] = $row['uid'];
            }

            // Total exercises in league.
            $data[] = $row['totalexer'];
            // Total exercises uploaded by the user.
            $data[] = $row['exeruplo'];
            // Total grade of the user in the league.
            $data[] = "<b><i>".$row['totalmark']."</i></b>";
            
            // Prevents a division by zero (put NaN instead).
            if($row['totalexer'] > 0){
                $data[] = number_format(($row['totalmark'] / ($row['totalexer'] * 100)) * 100, 2, ',', ' ') . ' %';
            }else{
                $data[] = get_string('nan', 'league');
            }
            
            // An explanation in draw case about why the user is ahead
            // (respect the follower user).
            $data[] = $row['notes'];

            $table->data[] = $data;
            
            // If there was a total draw, DO NOT CHANGE the
            // position (because is really the same).
            if($row['notes'] !== get_string('total_draw','league')){
                $position += 1;
            }
        }

        return \html_writer::table($table);
    }
    
    /**
     * Print an explanation about the acronym used on the table.
     * 
     * @param object $output The rederer output.
     * @return object The updated renderer output.
     */
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