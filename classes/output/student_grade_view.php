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
 * The renderer of students grades for every exercise.
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
 * The renderer class to show students grades to the league exercises.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_grade_view implements \renderable {
 
    /** @var object Array with the marks according to the exercises. */
    public $marks = null;
    
    /** @var int Course Module ID. */
    public $cmid = 0;
    
    /** @var int Context ID. */
    public $contextid = 0;
    
    /** @var bool Capability if an user can download attempts. */
    public $candownload = false;
    
    /**
     * Class constructor.
     * 
     * @param int $cmid Course Module ID. 
     * @param int $contextid Context ID. 
     * @param object $marks Array with the marks according to the exercises. 
     * @param bool $candownload Capability if an user can download attempts. 
     */
    public function __construct($cmid, $contextid = 0, $marks = null, $candownload = false) {
        $this->marks = $marks;
        $this->cmid = $cmid;
        $this->contextid = $contextid;
        $this->candownload = $candownload;
    }
    
    /**
     * Print the table with the grades for the student.
     * 
     * @return string HTML output string with the table.
     */
    public function print_grades(){

        // Create and initializating the table.
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

        // Foreach exercise that the user has upload or is enabled.
        // Prevents the teachers that users will see ever disabled 
        // exercises in the league.
        foreach ($this->marks as $mark){
            $mark = get_object_vars($mark);

            // Only print if the exercise is enabled or the
            // user has sent an attempt.
            if($mark['enabled'] == 1 || $mark['idat']){
                $this->marks = array();
                $this->marks[] = $mark['name'];
                // If there is an attempt show modification time.
                $this->marks[] = ($mark['tma'] ? date("H:i:s, d (D) M Y", $mark['tma']) : "");

                // If the exercise has an file ID associated, that 
                // means an attempt was sent. Make a URL to download. 
                if($mark['id_file']){
                    if($this->candownload){
                        $file = \league_model::restoreURLFile($this->contextid, $mark['id_file']);
                        if($file){
                            $this->marks[] = '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
                        }else{
                            $this->marks[] = get_string('cant_create_url', 'league');
                        }
                    }else{
                        $this->marks[] = get_string('usercantdownload', 'league');
                    }
                }

                // Text to print if the attempt is graded or not.
                if($mark['mark']){
                    if($mark['mark'] == -1){
                        $this->marks[] = get_string('no_mark_yet', 'league');
                    }else{
                        if($mark['published'] == 0){
                            $this->marks[] = get_string('no_mark_yet', 'league');
                        }else{
                            $this->marks[] = $mark['mark']."%";
                        }
                    }
                }else{
                    $this->marks[] = "<b><i>".get_string('not_sent_yet', 'league')."</i></b>";
                    $this->marks[] = "";
                }

                // Check if the attempt is graded and published.
                if($mark['mark'] == -1 || $mark['published'] == 0){
                    $this->marks[] = "";
                }else{
                    $this->marks[] = $mark['observations'];
                }

                $table->data[] = $this->marks;
            }
        }

        return \html_writer::table($table);
    }
}