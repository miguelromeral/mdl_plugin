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