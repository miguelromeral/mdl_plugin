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


function print_attempts_exercise($exercises, $cmid, $id_exer, $name, $contextid){
    
    $table = new html_table();
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
    $table->head = $headings;
    $table->align = $align;

    $na = array();
    
    foreach ($exercises as $d){
        $d = get_object_vars($d);
        $user = $d['firstname'] . " " . $d['lastname'];
        if(!isset($na[$user])){
            array_push($na, $user);
            $na[$user] = 0;
        }
            
        $na[$user] += 1;
        $na[$user ."_max"] = $na[$user];
    }
    
    foreach ($exercises as $d){
        $d = get_object_vars($d);
        $data = array();
        $data[] = get_user_image($d['id_user'], 40);
        $user = $d['firstname'] . " " . $d['lastname'];
        $data[] = $user;
        $data[] = date("H:i:s, d (D) M Y", $d['timemodified']);
        
        
        $data[] = $na[$user];
        
        $data[] = (($d['mark'] == -1) ?get_string('no_mark_yet', 'league') : $d['mark']."%");
        
        if($d['id_file']){
            $file = restoreURLFile($contextid, $d['id_file']);
            if($file){
                $data[] = '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
            }else{
                $data[] = get_string('cant_create_url', 'league');
            }
        }
        
        if($na[$user] == $na[$user ."_max"]){
            $data[] = '<form action="mark_student.php" method="get" >
                <input type="hidden" name="id" value="'. $cmid .'" />
                <input type="hidden" name="attempt" value="'. $d['id'] .'" />
                <input type="submit" value="'. get_string('mark_student_button', 'league') .'"/>
            </form>';
        }else{
            $data[] = "";
        }
        
        $na[$user] -= 1;
        
        $table->data[] = $data;
    }
    
    return html_writer::table($table);
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