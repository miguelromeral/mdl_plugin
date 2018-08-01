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
 * Class to create a qualy in function of the league instance.
 *
 * @package    mod_league
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

/**
 * Class to generate a qualy in function of the league instance.
 * 
 * With all appropiate data, this class generates a qualy.
 * 
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class league_qualy {
    
    /** @var int League ID. */
    private $leagueid = null;
    
    /** @var int Course ID. */
    private $courseid = null;
    
    /** @var string User role. */
    private $role = null;
    
    /** @var int Method to sort the qualy. */
    private $method = null;
    
    /** @var object An array of array with the qualy data. */
    private $qualy = null;
    
    /**
     * Load the class and generates automatically the qualy.
     * 
     * @param int $idleague League ID.
     * @param int $idcurso Course ID.
     * @param string $rol User role.
     * @param int $method Method to sort the qualy.
     */
    public function __construct($idleague, $idcurso, $rol, $method){
        $this->leagueid = $idleague;
        $this->courseid = $idcurso;
        $this->role = $rol;
        $this->method = $method;
        $this->qualy = $this->generate_qualy();
    }
    
    /**
     * Return the league's qualy.
     * 
     * @return object
     */
    public function get_qualy(){
        return $this->qualy;
    }
    
    /**
     * Generates the qualy with all role student users in the course.
     * 
     * @return object Array of array with the qualy sorted appropiately.
     */
    private function generate_qualy(){
        // Get all users in the course.
        $students = \league_model::get_students();
        $qualy = Array();
        // For the moment, WE DON'T SORT THE QUALY, only recover students data.
        foreach ($students as $student){
            $student = get_object_vars($student);
            
            // Get all the attempts data for this user: 
            // total exercises, total exercises uploaded, total mark earned
            // and number of non marked attempts.
            $attemptsdata = \league_model::get_qualy_data($this->leagueid, $student['id'], $this->role);
             
            // Only one row returned by the last query.
            foreach ($attemptsdata as $data){
                $data = get_object_vars($data);
                // Create an array with the user data.
                $row = Array();
                $row += array('name' => $student['firstname']." ".$student['lastname']);
                $row += array('uname' => $student['username']);
                $row += array('uid' => $student['id']);
                $row += array('totalexer' => $data['te']);
                $row += array('exeruplo' => $data['eu']);
                $row += array('totalmark' => $data['acum'] + $data['sc']);
                $row += array('marks' => \league_model::get_mark_array_by_student($this->leagueid, $student['id'], true));
                $row += array('notes' => "");
                $row += array('picture' => new user_picture(\league_model::get_user_by_id($student['id'])));
            }
            // Add the user data to the global qualy.
            array_push($qualy, $row);
        }

        // In function of the league method, sort the qualy on the right way.
        switch($this->method){
            case 1: return $this->sort_qualy_array_best_marks($qualy);
            case 2: return $this->sort_qualy_array_more_exercises($qualy);
                // Non sorted qualy by default.
            default: return $qualy;
        }
    }
    
    /**
     * Sort the qualy by best global mark, 
     * then by best individual marks 
     * and then by exercises uploaded.
     * 
     * @param object $qualy Qualy unsorted.
     * @return object Qualy sorted.
     */
    private function sort_qualy_array_best_marks($qualy){
        $size = sizeof($qualy);
        
        // Bubble sort algorithm.
        for ($i = 1; $i < $size; $i++){
            for($j = 0; $j < $size - $i; $j++){
                $leader = $qualy[$j];
                $follower = $qualy[$j+1];
                
                // Check if follower has best total mark than leader.
                $followerbestglobal = $follower['totalmark'] > $leader['totalmark'];
                $equalglobal = $follower['totalmark'] === $leader['totalmark'];
                // Check if the follower has best individual marks.
                $followerbestindividuals = $this->mejoresNotasSegundo($qualy, $leader, $follower);
                
                // If a switch is needed, do it!
                if($followerbestglobal or
                        ($equalglobal and $followerbestindividuals)){
                    $qualy = $this->exchange($qualy, $j, $j+1);
                }
            }
           }
           
        // Once is sorted, put the notes why a leader is ahead the follower.
        for ($i = 0; $i < $size - 1; $i++){
            $leader = $qualy[$i];
            $follower = $qualy[$i+1];
            $qualy = $this->set_notes_best_marks($qualy, $leader, $follower, $i, $i+1);
        }
        
        return $qualy;
    }

    /**
     * Sort the qualy by more exercises uploaded,
     * then by global best marks,
     * then by best individual marks.
     * 
     * @param object $qualy Qualy unsorted.
     * @return object Qualy sorted.
     */
    private function sort_qualy_array_more_exercises($qualy){
        $size = sizeof($qualy);
        
        // Bubble sort algorithm.
        for ($i = 1; $i < $size; $i++){
            for($j = 0; $j < $size - $i; $j++){
                $leader = $qualy[$j];
                $follower = $qualy[$j+1];
                
                // Check if the follower has more exercises uploaded.
                $followermoreexercises = $follower['exeruplo'] > $leader['exeruplo'];
                // Check if the follower has more exercises uploaded.
                $sameexercises = $follower['exeruplo'] == $leader['exeruplo'];
                // Check if the follower has best global mark.
                $followerbestglobal = $follower['totalmark'] > $leader['totalmark'];
                // Check if they have the same global mark.
                $sameglobal= $follower['totalmark'] == $leader['totalmark'];
                // Check if the follower has best individual marks.
                $followerbestindividuals = $this->mejoresNotasSegundo($qualy, $leader, $follower);
                
                // If a switch is needed, do it!
                if($followermoreexercises or 
                        ($sameexercises and 
                        ($followerbestglobal or 
                        ($sameglobal and $followerbestindividuals)))){
                    $qualy = $this->exchange($qualy, $j, $j+1);
                }
            }
           }
           
        // Once is sorted, put the notes why a leader is ahead the follower.
        for ($i = 0; $i < $size - 1; $i++){
            $leader = $qualy[$i];
            $follower = $qualy[$i+1];
            $qualy = $this->set_notes_more_exercises($qualy, $leader, $follower, $i);
        }
        
        return $qualy;
    }
    
    /**
     * Exchange two elements on an array.
     * 
     * @param array $array Array to switch between elements.
     * @param object $elementone element to switch.
     * @param object $elementtwo element to switch.
     * @return array Array with the elements switched.
     */
    private function exchange($array, $elementone, $elementtwo){
        $aux = $array[$elementone];
        $array[$elementone] = $array[$elementtwo];
        $array[$elementtwo] = $aux;
        return $array;
    }

    /**
     * Set aclarations why a leader is ahead the follower with best marks.
     * 
     * @param object $qualy Qualy.
     * @param object $leader Leader row.
     * @param object $follower Follower row.
     * @param int $li Leader index.
     * @param int $fi Follower index.
     * @return object Qualy with notes set. 
     */
    private function set_notes_best_marks($qualy, $leader, $follower, $li, $fi){
        $index = 0;
        // Get the leader total exercise uploaded.
        $exercisesuploaded = $leader['exeruplo'];
        // If the global mark is the same, check if the leader has more exercise uploaded.
        if($leader['exeruplo'] > $follower['exeruplo'] && $leader['totalmark'] === $follower['totalmark']){
            $qualy[$li]['notes'] = get_string('more_exercises_uploaded','league');
        }else{
            // If both users has the same global mark:
            if($leader['totalmark'] === $follower['totalmark']){
                // Continue until a note will be set or we reached the end
                // comparing their marks (in that case, total draw).
                while (true) {
                    if($exercisesuploaded != $index){
                        // We get the $index best individual mark of each one.
                        $leadermark = $leader['marks'][$index];
                        $followermark = $follower['marks'][$index];
                        // If there are both marks, that mean they uploaded,
                        // at least, $ind exercises. We compare it.
                        if($leadermark && $followermark){
                            // If the individual leader mark is higher...
                            if($leadermark > $followermark){
                                $qualy[$li]['notes'] = get_string('higher_mark','league').' ('. $this->compare_marks($qualy, $li, $fi, true).' > '
                                        . $this->compare_marks($qualy, $li, $fi, false).')';
                                return $qualy;
                            }
                            // If the individual $index mark is the same, check the next one.
                            if($leadermark == $followermark){
                                $index += 1;
                            }else{
                                // If the follower mark is not set yet (TBA), return the qualy.
                                if($followermark == get_string('q_tba', 'league')){
                                    return $qualy;
                                }
                            }
                        }else{
                            // If both two users has not mark in $index position, total draw.
                            $qualy[$li]['notes'] = get_string('total_draw','league');
                            return $qualy;
                        }
                    }else {
                        // If we reached the end, total draw.
                        $qualy[$li]['notes'] = get_string('total_draw','league');
                        return $qualy;
                    }
                }
            }
        }
        return $qualy;
    }
    
    /**
     * Set aclarations why a leader is ahead the follower with more exercises.
     * 
     * @param object $qualy Qualy.
     * @param object $leader Leader row.
     * @param object $follower Follower row.
     * @param int $li Leader index.
     * @return object Qualy with notes set. 
     */
    private function set_notes_more_exercises($qualy, $leader, $follower, $li){
        $index = 0;
        // Get the leader total exercise uploaded.
        $exercisesuploaded = $leader['exeruplo'];
        // If the leader has more exercises uploaded...
        if($leader['exeruplo'] > $follower['exeruplo']){
            $qualy[$li]['notes'] = get_string('more_exercises_uploaded','league');
        }else{
            // If the global mark is the same, we check the individual ones.
            if($leader['totalmark'] === $follower['totalmark']){
                while (true) {
                    // If there are individual marks availables:
                    if($exercisesuploaded != $index){
                        // Get the individual marks on index $index.
                        $leadermark = $leader['marks'][$index];
                        $followermark = $follower['marks'][$index];
                        if($leadermark && $followermark){
                            // If there are a better individual mark.
                            if($leadermark > $followermark){
                                $qualy[$li]['notes'] = get_string('higher_mark','league').' ('. $this->compare_marks($qualy, $li, $li + 1, true).' > '
                                        . $this->compare_marks($qualy, $li, $li + 1, false) . ')';
                                return $qualy;
                            }
                            // If is the same, continue
                            $index += 1;
                            if($leadermark != $followermark){
                                // If the follower has a mark with no mark yet.
                                if($followermark == get_string('q_tba', 'league')){
                                    return $qualy;
                                }
                            }
                        }else{
                            // There already are no more individual marks.
                            $qualy[$li]['notes'] = get_string('total_draw','league');
                            return $qualy;
                        }
                    }else{
                        // If we reached the end, total draw.
                        $qualy[$li]['notes'] = get_string('total_draw','league');
                        return $qualy;
                    }
                } 
            }
        }
        
        return $qualy;
    }

    /**
     * Compare between two marks and get the appropiate (the highest or smallest one).
     * 
     * @param object $qualy Qualy sorted.
     * @param int $li Leader index.
     * @param int $fi Follower index.
     * @param bool $highest Return the highest mark.
     * @return int Highest (or slowest) mark. 
     */
    private function compare_marks($qualy, $li, $fi, $highest){
        // Get all individual marks.
        $leader = $qualy[$li]['marks'];
        $follower = $qualy[$fi]['marks'];
        $index = 0;
        // Seek for each individual mark until a difference is found.
        while (true) {
            // Get individual marks.
            $n1 = (isset($leader[$index]) ? $leader[$index] : -1);
            $n2 = (isset($follower[$index]) ? $follower[$index] : -1);
            // Difference found.
            if($n2 != $n1){
                return ($highest ? $n1 : $n2);
            }
            if($n1 == $n2){
                // If there are the same and the leader one is under TBA, return it.
                if($n1 == -1){
                    return get_string('q_tba', 'league');
                }
                $index += 1;
            }
        }
    }

    /**
     * Return true if the follower has best individual marks.
     * 
     * @param object $qualy Qualy.
     * @param object $leader Current leader row.
     * @param object $follower Current follower row.
     * @return boolean
     */
    private function mejoresNotasSegundo($qualy, $leader, $follower){
        $index = 0;
        // Max of uploaded exercises.
        $size = max($leader['exeruplo'], $follower['exeruplo']);
        while (true) {
            // Until we reach the end
            if($index != $size){
                // Get individual marks on index $index.
                $n1 = ($leader['marks'][$index] ? $leader['marks'][$index] : null);
                $n2 = ($follower['marks'][$index] ? $follower['marks'][$index] : null);
                //If there are marks, check them.
                if($n1 && $n2){
                    if($n2 > $n1){
                        return true;
                    }
                    if($n1 > $n2){
                        return false;
                    }
                    if($n1 == $n2){
                        $index += 1;
                    }
                }else{
                    if($n1){
                        return false;
                    }
                    if($n2){
                        return true;
                    }
                }
            }else{
                return false;
            }
        }
    }

}
    