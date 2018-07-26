<?php

function get_user_image($iduser, $size){
    global $COURSE, $DB, $OUTPUT;
    $cContext = context_course::instance($COURSE->id);
    $query = 'select u.id as id, firstname, lastname, picture, imagealt, '
            . 'email, u.* from mdl_role_assignments as a, mdl_user as u where '
            . 'contextid=' . $cContext->id . ' and roleid=5 and a.userid=u.id';
    $rs = $DB->get_recordset_sql( $query );
    foreach( $rs as $r ) {
        if($r->id == $iduser){
            return $OUTPUT->user_picture($r, array('courseid'=>$COURSE->id));
            //return $OUTPUT->user_picture($r, array('size' => $size, 'courseid'=>$COURSE->id));
        }
    }
    return null;
}

function print_table_grades($filas, $tablecolumns, $tableheaders, $ex_name, $url){
    
    $table = new flexible_table('mod-league-grade');
    $table->define_baseurl($url);
    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->sortable(true);
    $table->no_sorting('userpic');
    $table->collapsible(false);
    $table->setup();
    
    
    if ($orderby = $table->get_sql_sort()) {
        $filas = sort_grade_rows($filas, $orderby, $tableheaders, $ex_name);
    }
    
    $table->initialbars(true);
    
    foreach($filas as $r){
        $table->add_data($r);
    }
    
    $table->print_html();
    
}


function sort_grade_rows($rows, $sortby, $headers, $ex_name){
    $del = explode(', ', $sortby);
    
    foreach($del as $d){
        $n = explode(' ', $d);
        
       // echo "<br>";
       // echo $n[0]. " - ". $n[1];
       // echo "<br>";
        
        $ind = 0;
        switch($n[0]){
            case 'userpic': $ind = 0; break;
            case 'student': $ind = 1; break;
            default:
                array_shift($headers);
                array_shift($headers);
                //print_r($headers);
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
        //echo "Antes:<br>";
        //print_r($rows);
        
        $rows = array_sort($rows, $ind, $sort);
        
        ////echo "Después:<br>";
        //print_r($rows);
        return $rows;
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