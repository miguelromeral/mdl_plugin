<?php

function print_exercises($rol, $cmid, $data){
    if ($rol == 'teacher'){
    
        $table = new html_table();
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
        
        foreach ($data as $exer)
        {
            $exer = json_decode(json_encode($exer), True);
            $data = array();
            $data[] =  $exer['name'];
            $data[] =  date("H:i:s, d (D) M Y", $exer['timemodified']);
            $data[] =  ($exer['enabled'] == 0 ? 
                '<img src="images/no.png" width="30" height="30"/>' : 
                '<img src="images/yes.png" width="30" height="30"/>');
            $data[] =  ($exer['published'] == 0 ? 
                '<img src="images/no.png" width="30" height="30"/>' : 
                '<img src="images/yes.png" width="30" height="30"/>');
            
            $data[] = '<form action="view.php" method="post" >
                <input type="hidden" name="id" value="'.$cmid.'" />
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id_exer" value="'.$exer['id'].'" />
                <input type="hidden" name="exer_name" value="'.$exer['name'].'" />
                <input type="hidden" name="exer_description" value="'.$exer['statement'].'" />
                <input type="hidden" name="exer_enabled" value="'.$exer['enabled'].'" />
                <input type="hidden" name="exer_published" value="'.$exer['published'].'" />
                <input type="submit" value="'.get_string('del', 'league').'"/>
            </form>';
            
            $data[] = '<form action="add_exercise.php" method="get" >
                <input type="hidden" name="id" value="'.$cmid.'" />
                <input type="hidden" name="id_exer" value="'.$exer['id'].'" />
                <input type="hidden" name="name" value="'.$exer['name'].'" />
                <input type="hidden" name="statement" value="'.$exer['statement'].'" />
                <input type="submit" value="'.get_string('modify_exercise_button', 'league').'"/>
            </form>';
            
            $data[] = '<form action="view.php" method="post" >
                <input type="hidden" name="id" value="'. $cmid .'" />
                <input type="hidden" name="action" value="enable_disable" />
                <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                <input type="hidden" name="exer_name" value="'. $exer['name'] .'" />
                <input type="hidden" name="exer_description" value="'. $exer['statement'] .'" />
                <input type="hidden" name="exer_enabled" value="'. $exer['enabled'] .'" />
                <input type="hidden" name="exer_published" value="'. $exer['published'] .'" />
                <input type="submit" value="'. 
                ($exer['enabled'] == 0 ? get_string('enable_exercise_button', 'league') : get_string('disable_exercise_button', 'league')) 
            .'"/>
            </form>';
            
            $data[] = '<form action="marking.php" method="post" >
                <input type="hidden" name="id" value="'. $cmid .'" />
                <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                <input type="hidden" name="name" value="'. $exer['name'] .'" />
                <input type="submit" value="'. get_string('mark_exercise', 'league') .'"/>
            </form>';
            
            $data[] = '<form action="view.php" method="post" >
                <input type="hidden" name="id" value="'. $cmid .'" />
                <input type="hidden" name="action" value="publish" />
                <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                <input type="hidden" name="exer_name" value="'. $exer['name'] .'" />
                <input type="hidden" name="exer_description" value="'. $exer['statement'] .'" />
                <input type="hidden" name="exer_published" value="'. $exer['published'] .'" />
                <input type="hidden" name="exer_enabled" value="'. $exer['enabled'] .'" />
                <input type="submit" value="'. 
                ($exer['published'] == 0 ? get_string('publish', 'league') : get_string('unpublish', 'league')) 
            .'"/>
            </form>';
            
            $table->data[] = $data;
        }
        
        return html_writer::table($table);
        

    } else if ($rol == 'student'){
        
        $table = new html_table();
        $headings = array();
        $align = array();
        array_push($headings, get_string('exercise', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('timemofied', 'league'));
        array_push($align, 'center');
        array_push($headings, get_string('send_exercise', 'league'));
        array_push($align, 'center');
        $table->head = $headings;
        $table->align = $align;
        
        foreach ($data as $exer)
        {
            $exer = json_decode(json_encode($exer), True);
            if($exer['enabled'] == 1){
                $data = array();
                $data[] =  $exer['name'];
                $data[] =  date("H:i:s, d (D) M Y", $exer['timemodified']);
     
                $data[] = '<form action="upload.php" method="post" >
                    <input type="hidden" name="id" value="'. $cmid .'" />
                    <input type="hidden" name="action" value="begin" />
                    <input type="hidden" name="id_exer" value="'. $exer['id'] .'" />
                    <input type="hidden" name="name" value="'. $exer['name'] .'" />
                    <input type="hidden" name="statement" value="'. $exer['statement'] .'" />
                    <input type="submit" value="'. get_string('upload_exercise', 'league') .'"/>
                </form>';

                $table->data[] = $data;
            }
        }
        
        return html_writer::table($table);
    }
}

function print_notas_alumno($data, $contextid){
    
    $table = new html_table();
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
    
    foreach ($data as $d){
        $d = get_object_vars($d);
        
        if($d['enabled'] == 1 || $d['idat']){
            $data = array();
            $data[] = $d['name'];
            $data[] = ($d['tma'] ? date("H:i:s, d (D) M Y", $d['tma']) : "");
            
            if($d['id_file']){
                $file = restoreURLFile($contextid, $d['id_file']);
                if($file){
                    $data[] = '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
                }else{
                    $data[] = get_string('cant_create_url', 'league');
                }
            }
            
            if($d['mark']){
                if($d['mark'] == -1){
                    $data[] = get_string('no_mark_yet', 'league');
                }else{
                    if($d['published'] == 0){
                        $data[] = get_string('no_mark_yet', 'league');
                    }else{
                        $data[] = $d['mark']."%";
                    }
                }
            }else{
                $data[] = "<b><i>".get_string('not_sent_yet', 'league')."</i></b>";
            }
            
            if($d['mark'] == -1 || $d['published'] == 0){
                $data[] = "";
            }else{
                $data[] = $d['observations'];
            }
            
            $table->data[] = $data;
        }
    }
    
    return html_writer::table($table);
}


function print_qualy($q, $rol = 'student', $iduser = -1){
    $pos = 1;
    
    $table = new html_table();
    $headings = array();
    $align = array();
    array_push($headings, get_string('q_pos', 'league'));
    array_push($align, 'center');
    
    if($rol == 'student'){
        array_push($headings, get_string('q_name_hashed', 'league'));
        array_push($align, 'center');
    }else{
        array_push($headings, get_string('q_name', 'league'));
        array_push($align, 'center');
    }
    
    if($rol == 'teacher'){ 
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
    
    if($rol == 'teacher' && $iduser != -1){ 
        array_push($headings, get_string('q_best_marks', 'league'));
        array_push($align, 'center');
    }
    
    $table->head = $headings;
    $table->align = $align;
    
    foreach ($q as $r){
        $data = array();
        $data[] = $pos;

        if($rol == 'teacher'){ 
            $data[] = $r['name'];
            $data[] = $r['uname'];
        } else if($rol == 'student'){ 
            if($r['uid'] == $iduser){
                $data[] = $r['name'];
            } else {
                $data[] = md5($r['name']." - ".$r['uname']);
            }
        } 

        if($rol == 'teacher'){
            $data[] = $r['uid'];
        }
        
        $data[] = $r['totalexer'];
        $data[] = $r['exeruplo'];
        $data[] = $r['totalmark'];
        $data[] = ($r['totalexer'] > 0 ? number_format(($r['totalmark'] / ($r['totalexer'] * 100)) * 100, 2, ',', ' ') . ' %' : 'NaN');
        $data[] = $r['notes'];
        if($rol === 'teacher' && $iduser != -1){
            foreach ($r['marks'] as $n){
                if($n){
                    $data[] = $n;
                }
            }
        }
        
        $table->data[] = $data;
        $pos += 1;
    }
    
    return html_writer::table($table);
}


function print_students_exercise($exercises, $cmid, $id_exer, $name, $contextid){
    
    $table = new html_table();
    $headings = array();
    $align = array();
    array_push($headings, get_string('student', 'league'));
    array_push($align, 'center');
    array_push($headings, get_string('upload_time', 'league'));
    array_push($align, 'center');
    array_push($headings, get_string('mark', 'league'));
    array_push($align, 'center');
    $table->head = $headings;
    $table->align = $align;

    foreach ($exercises as $d){
        $d = get_object_vars($d);
        $data = array();
        $data[] = $d['firstname'];
        $data[] = date("H:i:s, d (D) M Y", $d['timemodified']);
        $data[] = (($d['mark'] == -1) ?get_string('no_mark_yet', 'league') : $d['mark']."%");
        
        if($d['id_file']){
            $file = restoreURLFile($contextid, $d['id_file']);
            if($file){
                $data[] = '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
            }else{
                $data[] = get_string('cant_create_url', 'league');
            }
        }
        
        $data[] = '<form action="mark_student.php" method="post" >
                <input type="hidden" name="id" value="'. $cmid .'" />
                <input type="hidden" name="id_exer" value="'. $id_exer .'" />
                <input type="hidden" name="name" value="'. $name .'" />
                <input type="hidden" name="id_user" value="'. $d['id_user'] .'" />
                <input type="hidden" name="idat" value="'. $d['id'] .'" />
                <input type="hidden" name="mark" value="'. $d['mark'] .'" />
                <input type="hidden" name="observations" value="'. $d['observations'] .'" />
                <input type="submit" value="'. get_string('mark_student_button', 'league') .'"/>
            </form>';
        
        $table->data[] = $data;
    }
    
    return html_writer::table($table);
}
