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
 * The renderer of total attempts sent to an exercise.
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
require_once($CFG->dirroot.'/mod/league/classes/model.php');

/**
 * The renderer class to show attempts to an exercise.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class total_attempts_view implements \renderable {

    /** @var int Course Module ID. */
    private $cmid = 0;
    
    /** @var object Array with attempts data. */
    private $attempts = null;
    
    /** @var int Exercise ID. */
    private $exerciseid = 0;
    
    /** @var int Context ID. */
    private $contextid = 0;
    
    /** @var string Exercise name. */
    public $name = null;
    
    /**
     * Class constructor.
     * 
     * @param int $cmid Course Module ID.
     * @param object $attempts Array with attempts data.
     * @param int $idexer Exercise ID.
     * @param string $name Exercise name.
     * @param int $contextid Context ID.
     */
    public function __construct($cmid, $attempts, $idexer, $name, $contextid) {
        $this->cmid = $cmid;
        $this->exerciseid = $idexer;
        $this->attempts = $attempts;
        $this->name = $name;
        $this->contextid = $contextid;
    }
    
    /**
     * Check if there are attempts to this exercise.
     * 
     * @return bool
     */
    public function attempts_exist(){
        return $this->attempts != null;
    }
    
    /**
     * Print a table with the attempts data.
     * 
     * @return string HTML output string.
     */
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
        array_push($headings, '');
        array_push($align, 'center');
        array_push($headings, '');
        array_push($align, 'center');
        $table->head = $headings;
        $table->align = $align;

        $numberattempts = array();

        // Here we get the attempts number by each user who has
        // sent an attempt (in this way, later we could print
        // the number attempt in the table).
        foreach ($this->attempts as $attempt){
            $attempt = get_object_vars($attempt);
            $user = $attempt['firstname'] . " " . $attempt['lastname'];
            if(!isset($numberattempts[$user])){
                array_push($numberattempts, $user);
                $numberattempts[$user] = 0;
            }

            $numberattempts[$user] += 1;
            $numberattempts[$user ."_max"] = $numberattempts[$user];
        }

        // Right here we make the rows to the table.
        foreach ($this->attempts as $attempt){
            $attempt = get_object_vars($attempt);
            $data = array();
            // User picture.
            $data[] = \league_model::get_user_image($attempt['user']);
            // User name.
            $user = $attempt['firstname'] . " " . $attempt['lastname'];
            $data[] = $user;
            // Attempt modified time.
            $data[] = date("H:i:s, d (D) M Y", $attempt['timemodified']);

            // Number of attempt to this user.
            // An user can sent many attempts (if is set),
            // this number belongs to the count of what attempts
            // the user has sent. 
            $data[] = $numberattempts[$user];

            // Current mark.
            $data[] = (($attempt['mark'] == -1) ?get_string('no_mark_yet', 'league') : $attempt['mark']."%");

            // Link with the attempt to download.
            // Here there is no capability check to download because
            // it's supposed the only way to be here is having that
            // capability.
            if($attempt['itemid']){
                $file = \league_model::restoreURLFile($this->contextid, $attempt['itemid']);
                if($file){
                    $data[] = '<a href="'.$file.'">'.get_string('download_file_button', 'league')."</a>";
                }else{
                    $data[] = get_string('cant_create_url', 'league');
                }
            }

            // We check this is the last attempt sent by an specefic user.
            // If there is, create a button to redirect to mark the attempt.
            if($numberattempts[$user] == $numberattempts[$user ."_max"]){
                $data[] = '<form action="mark_student.php" method="get" >
                    <input type="hidden" name="id" value="'. $this->cmid .'" />
                    <input type="hidden" name="attempt" value="'. $attempt['id'] .'" />
                    <input type="submit" value="'. get_string('mark_student_button', 'league') .'"/>
                </form>';
            }else{
                $data[] = "";
            }
            $numberattempts[$user] -= 1;

            $table->data[] = $data;
        }

        return \html_writer::table($table);
    }
}