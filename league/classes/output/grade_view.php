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
 * The renderer of individual grades for each exercises.
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
require_once($CFG->libdir . '/tablelib.php');

/**
 * The renderer class to print a table with all individual grades 
 * for each student on each exercise.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacher_grade_view implements \renderable {
 
    /** @var object Rows with the user and grades information. */
    public $rows = null;
    
    /** @var object Array with the columns names of the table. */
    public $tablecolumns = null;
    
    /** @var object Array with the headers of the table. */
    public $tableheaders = null;
    
    /** @var object Array with the name of every exercise. */
    public $exercisesnames = null;
    
    /** @var string URL to redirect when click to sort the table. */
    public $url = null;
    
    /**
     * Class constructor.
     * 
     * @param object $rows Rows with the user and grades information. 
     * @param object $tablecolumns Array with the columns names of the table. 
     * @param object $tableheaders Array with the headers of the table. 
     * @param object $exercisesnames Array with the name of every exercise.
     * @param string $url URL to redirect when click to sort the table. 
     */
    public function __construct($rows, $tablecolumns, $tableheaders, $exercisesnames, $url) {
        $this->rows = $rows;
        $this->tablecolumns = $tablecolumns;
        $this->tableheaders = $tableheaders;
        $this->exercisesnames = $exercisesnames;
        $this->url = $url;
    }
    
    /**
     * Print directly the table sorted by the user preferences.
     */
    function print_table_grades(){

        // Create the flexible table with all appropiate data.
        $table = new \flexible_table('mod-league-grade');
        $table->define_baseurl($this->url);
        $table->define_columns($this->tablecolumns);
        $table->define_headers($this->tableheaders);
        // Sortable but the user picture (don't have sense at all).
        $table->sortable(true);
        $table->no_sorting('userpic');
        $table->collapsible(false);
        // Begining to "operate" the table.
        $table->setup();

        // If user wants to sort the table, check what criteria.
        if ($orderby = $table->get_sql_sort()) {
            $this->rows = $this->sort_grade_rows($orderby);
        }

        $table->initialbars(true);

        // Once we have the data sorted, add every row to the table.
        foreach($this->rows as $r){
            $table->add_data($r);
        }

        // Print all html content on the page (without HTML string).
        $table->print_html();
    }
        
    /**
     * Sort the rows with the user preferences.
     * 
     * @param string $sortby Criterion to sort (format: "columnname ASC | DESC").
     * @return object rows sorted appropiately.
     */
    function sort_grade_rows($sortby){
        // Cut the initial string to get only the first one.
        $cut = explode(', ', $sortby);
        $headers = $this->tableheaders;
        
        // If a criterion exists, get the first one and sort the rows.
        foreach($cut as $criterion){
            // We parse the string to get:
            // $[0] --> columnname (to sort by).
            // $[1] --> sort criterion (ASC | DESC).
            $parsed = explode(' ', $criterion);
            
            // Check what index of column we have to order by.
            // Picture is 0, student name is 1, first exercise will
            // be 2, the second one 3 and so on.
            // We compare the headers to get the appropiate index column.
            $index = 0;
            switch($parsed[0]){
                case 'userpic': $index = 0; break;
                case 'student': $index = 1; break;
                default:
                    // Right here we want to sort by one of the
                    // exercises name. Let's check it!
                    array_shift($headers);
                    array_shift($headers);
                    
                    // For each exercise name...
                    foreach($headers as $indexkey => $header){
                        // If the sort criterion match with 
                        // the header (exercise name), this 
                        // will be the index on the table.
                        // We add 2 due to the userpic and 
                        // student columns.
                        if($parsed[0] == $header){
                            $index = 2 + $indexkey;
                            break;
                        }
                    }
            }

            // Put the order (ASC or DESC).
            $sort = null;
            switch($parsed[1]){
                case 'ASC': $sort = SORT_ASC;
                    break;
                case 'DESC': $sort = SORT_DESC;
                    break;
            }
            
            // Sort the array in function of the index.
            $this->rows = $this->array_sort($this->rows, $index, $sort);

            // Once we have ordered the rows, return them.
            return $this->rows;
        }

    }

    /**
     * Function to sort an array by the official manual
     * (@see http://php.net/manual/es/function.sort.php).
     * 
     * @param object $array Array of arrays to sort.
     * @param int $on Index of the column to array.
     * @param string $order Criterion to sort.
     * @return object Array of arrays sorted.
     */
    function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
    
}
