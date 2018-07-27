<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');

class grade_view implements \renderable {
 
    public $rows = null;
    public $tablecolumns = null;
    public $tableheaders = null;
    public $ex_name = null;
    public $url = null;
    
    public function __construct($rows, $tablecolumns, $tableheaders, $ex_name, $url) {
        $this->rows = $rows;
        $this->tablecolumns = $tablecolumns;
        $this->tableheaders = $tableheaders;
        $this->ex_name = $ex_name;
        $this->url = $url;
    }
    
    
    function print_table_grades(){

        $table = new \flexible_table('mod-league-grade');
        $table->define_baseurl($this->url);
        $table->define_columns($this->tablecolumns);
        $table->define_headers($this->tableheaders);
        $table->sortable(true);
        $table->no_sorting('userpic');
        $table->collapsible(false);
        $table->setup();


        if ($orderby = $table->get_sql_sort()) {
            $this->rows = $this->sort_grade_rows($orderby);
        }

        $table->initialbars(true);

        foreach($this->rows as $r){
            $table->add_data($r);
        }

        $table->print_html();

    }
    
    

    function sort_grade_rows($sortby){
        $del = explode(', ', $sortby);
        $headers = $this->tableheaders;
        
        foreach($del as $d){
            $n = explode(' ', $d);

            $ind = 0;
            switch($n[0]){
                case 'userpic': $ind = 0; break;
                case 'student': $ind = 1; break;
                default:
                    array_shift($headers);
                    array_shift($headers);
                    foreach($headers as $k => $h){
                        if($n[0] == $h){
                            $ind = 2 + $k;
                            break;
                        }
                    }
            }

            $sort = null;
            switch($n[1]){
                case 'ASC': $sort = SORT_ASC;
                    break;
                case 'DESC': $sort = SORT_DESC;
                    break;
            }
            $this->rows = $this->array_sort($this->rows, $ind, $sort);

            return $this->rows;
        }



    }

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
