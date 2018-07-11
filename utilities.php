<?php

function print_exercises($idliga, $rol, $cmid){
    global $DB;
    $var="SELECT * 
    FROM mdl_league_exercise
    WHERE league = $idliga
    ORDER BY id";
    $data = $DB->get_records_sql($var);
    
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
        array_push($headings, "");
        array_push($align, 'center');
        array_push($headings, "");
        array_push($align, 'center');
        array_push($headings, "");
        array_push($align, 'center');
        array_push($headings, "");
        array_push($align, 'center');
        array_push($headings, "");
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
        
        echo html_writer::table($table);
        

    } else if ($rol == 'student'){
        
        ?>

<table>
    <tr>
        <th><?= get_string('exercise', 'league') ?></th>
        <th><?= get_string('timemofied', 'league') ?></th>
        <th><?= get_string('send_exercise', 'league') ?></th>
    </tr>
    
    <?php
    foreach ($data as $exer)
    {
        $exer = json_decode(json_encode($exer), True);
        
        if($exer['enabled'] == 1){
        
        ?>
    <tr>
        <td id="thb"><?= $exer['name'] ?></td>
        <td><?= date("H:i:s, d (D) M Y", $exer['timemodified']) ?></td>
        <td>
             <form action="upload.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="action" value="begin" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="statement" value="<?= $exer['statement'] ?>" />
                <input type="submit" value="<?= get_string('upload_exercise', 'league') ?>"/>
            </form>
        </td>
    </tr>
        <?php
        }
    }
    
?>
</table>
<?php
        
    }
}

function print_students_exercise($cmid, $id_exer, $name, $contextid){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_league_attempt as a
    inner join (
            select max(c.id) as id, c.id_user, d.firstname, d.lastname
            from mdl_league_attempt as c
            inner join mdl_user as d
            on c.id_user = d.id
            where c.exercise = $id_exer
            group by c.id_user
            order by c.id desc
    ) as b
    on a.id = b.id
    group by b.id_user";
    $data = $DB->get_records_sql($var);
    ?>

<h1><?= $name ?></h1>

<table>
    <tr>
        <th><?= get_string('student', 'league') ?></th>
        <th><?= get_string('upload_time', 'league') ?></th>
        <th colspan="3"><?= get_string('mark', 'league') ?></th>
    </tr>
    
    <?php
    
    foreach ($data as $d){
        $d = get_object_vars($d);
        ?> <tr> 
            <td id="thb"><?php echo $d['firstname']." ".$d['lastname']; ?></td>
            <td><?= date("H:i:s, d (D) M Y", $d['timemodified']) ?></td>
            <td id="thb"><?php 
            if($d['mark'] == -1){
                echo get_string('no_mark_yet', 'league');
            }else{
                echo $d['mark']."%";
            }
            ?></td>
            <td>
                <?php
                if($d['id_file']){
                    $file = restoreURLFile($contextid, $d['id_file']);
                    if($file){
                        echo '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
                    }else{
                        echo get_string('cant_create_url', 'league');
                    }
                }
                ?>
            </td>
            <td>
             <form action="mark_student.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
                <input type="hidden" name="name" value="<?= $name ?>" />
                <input type="hidden" name="id_user" value="<?= $d['id_user'] ?>" />
                <input type="hidden" name="idat" value="<?= $d['id'] ?>" />
                <input type="hidden" name="mark" value="<?= $d['mark'] ?>" />
                <input type="hidden" name="observations" value="<?= $d['observations'] ?>" />
                <input type="submit" value="<?= get_string('mark_student_button', 'league') ?>"/>
            </form>
            </td>
        
        </tr>
        <?php
    }
    ?>
</table>

<?php
    
}


function getIDFileFromContenthash($contenthash){
    global $DB;
    $var="SELECT max(id) as 'm'
    FROM mdl_files
    WHERE contenthash = '$contenthash'";

    $data = $DB->get_records_sql($var);
    $id = -1;
    foreach ($data as $d){
        //print_r($d);
        foreach($d as $i => $l){
            $id = $l;
        }
    }
    
    return $id;
}

function print_notas_alumno($idleague, $cmid, $userid, $contextid){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_league_exercise as a
    left outer join
    (
        select a.id as idat, a.timemodified as tma,
		a.observations, a.name as fname,
		a.exercise, b.id_user, a.mark, a.id_file, a.url
		from mdl_league_attempt as a
		inner join (
			select max(id) as m, id_user
			from mdl_league_attempt
			where id_user = $userid
			group by exercise
		) as b
		on a.id = b.m
    ) as b
    on a.id = b.exercise
    where a.league = $idleague";
    $data = $DB->get_records_sql($var);
    ?>

<table>
    <tr>
        <th><?= get_string('exercise', 'league') ?></th>
        <th><?= get_string('upload_time', 'league') ?></th>
        <th><?= get_string('file_uploaded', 'league') ?></th>
        <th><?= get_string('mark', 'league') ?></th>
        <th><?= get_string('reviews', 'league') ?></th>
    </tr>
    
    <?php
    
    foreach ($data as $d){
        $d = get_object_vars($d);
        if($d['enabled'] == 1 || $d['idat']){
        
        
        ?> <tr> 
            <td id="thb"><?= $d['name'] ?></td>
            <td><?= ($d['tma'] ? date("H:i:s, d (D) M Y", $d['tma']) : "") ?></td>
            <td>
                <?php
                if($d['id_file']){
                    $file = restoreURLFile($contextid, $d['id_file']);
                    if($file){
                        echo '<a href="'.$file->url.'">'.get_string('download_file_button', 'league')."</a>";
                    }else{
                        echo get_string('cant_create_url', 'league');
                    }
                }
                ?>
            </td>
            <td id="thb"><?php 
            if($d['mark']){
                if($d['mark'] == -1){
                    echo get_string('no_mark_yet', 'league');
                }else{
                    if($d['published'] == 0){
                        echo get_string('no_mark_yet', 'league');
                    }else{
                        echo $d['mark']."%";
                    }
                }
            }else{
                echo "<b><i>".get_string('not_sent_yet', 'league')."</i></b>";
            }
            ?></td>
            <td><?php
            if($d['mark'] == -1 || $d['published'] == 0){
                echo "";
            }else{
                echo $d['observations'];
            }
            ?></td>
        
        </tr>
        <?php
        }
    }
    ?>
</table>

<?php
}

function get_qualy_array($idleague, $idcurso, $rol, $method){
    global $DB;
    //Lista de estudiantes de un curso
    $var="SELECT DISTINCT u.id AS userid, c.id AS courseid, u.firstname, u.lastname, u.username
    FROM mdl_user u
    JOIN mdl_user_enrolments ue ON ue.userid = u.id
    JOIN mdl_enrol e ON e.id = ue.enrolid
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
    JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id
    JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student'
    WHERE e.status = 0 AND u.suspended = 0 AND u.deleted = 0
      AND (ue.timeend = 0 OR ue.timeend > NOW()) AND ue.status = 0 and c.id = $idcurso";
    $data = $DB->get_records_sql($var);
    $q = Array();
    foreach ($data as $d){
        $d = get_object_vars($d);
        $var2 = "select count(id) as te, count(idat) as eu, sum(mark) as acum, COUNT(CASE WHEN mark = -1 THEN 1 END) as sc
        from mdl_league_exercise as a
        left outer join
        (
            select a.id as idat, a.timemodified as tma,
                    a.observations, a.name as fname,
                    a.exercise, b.id_user, a.mark, a.id_file, a.url
                    from mdl_league_attempt as a
                    inner join (
                            select max(id) as m, id_user
                            from mdl_league_attempt
                            where id_user = ${d['userid']}
                            group by exercise
                    ) as b
                    on a.id = b.m
        ) as b
        on a.id = b.exercise
        where a.league = $idleague";
        if($rol == 'student'){
            $var2 .= " and a.published = 1";
        }
        $data2 = $DB->get_records_sql($var2);
        foreach ($data2 as $d2){
            $d2 = get_object_vars($d2);
            $fila = Array();
            $fila += array('name' => $d['firstname']." ".$d['lastname']);
            $fila += array('uname' => $d['username']);
            $fila += array('uid' => $d['userid']);
            $fila += array('totalexer' => $d2['te']);
            $fila += array('exeruplo' => $d2['eu']);
            $fila += array('totalmark' => $d2['acum'] + $d2['sc']);
            $fila += array('marks' => getArrayMarkByStudent($idleague, $d['userid'], true));
            $fila += array('notes' => "");
        }
        array_push($q, $fila);
    }
    
    switch($method){
        case 1: return sort_qualy_array_best_marks($q);
        case 2: return sort_qualy_array_more_exercises($q);
        default: return $q;
    }
}

function exchange($array, $id1, $id2){
    $aux = $array[$id1];
    $array[$id1] = $array[$id2];
    $array[$id2] = $aux;
    return $array;
}

function sort_qualy_array_best_marks($q){
    $n = sizeof($q);
    //Algoritmo burbuja
    for ($i = 1; $i < $n; $i++){
        for($j = 0; $j < $n - $i; $j++){
            $r1 = $q[$j];
            $r2 = $q[$j+1];
            //echo "<br> Miro ".$j." y ".($j+1)." ( ${r1['totalmark']} / ${r2['totalmark']}) <br>";
            if($r2['totalmark'] > $r1['totalmark'] || ($r2['totalmark'] === $r1['totalmark'] && mejoresNotasSegundo($q, $r1, $r2))){
               // echo "<br>CAMBIO<br>";
                $q = exchange($q, $j, $j+1);
            }
        }
       }
    //Ya está ordenado, ahora a poner las aclaraciones en caso de empates
    for ($i = 0; $i < $n - 1; $i++){
        $r1 = $q[$i];
        $r2 = $q[$i+1];
        $q = setNotesBM($q, $r1, $r2, $i, $i+1);
    }
    return $q;
}

function sort_qualy_array_more_exercises($q){
    $n = sizeof($q);
    //Algoritmo burbuja
    for ($i = 1; $i < $n; $i++){
        for($j = 0; $j < $n - $i; $j++){
            $r1 = $q[$j];
            $r2 = $q[$j+1];
            //echo "<br> Miro ".$j." y ".($j+1)." ( ${r1['totalmark']} / ${r2['totalmark']}) <br>";
            if($r2['exeruplo'] > $r1['exeruplo'] ||
                    ($r2['exeruplo'] === $r1['exeruplo'] && 
                        ($r2['totalmark'] > $r1['totalmark'] || mejoresNotasSegundo($q, $r1, $r2)))){
                //echo "<br>CAMBIO<br>";
                $q = exchange($q, $j, $j+1);
            }
        }
       }
    //Ya está ordenado, ahora a poner las aclaraciones en caso de empates
    for ($i = 0; $i < $n - 1; $i++){
        $r1 = $q[$i];
        $r2 = $q[$i+1];
        $q = setNotesME($q, $r1, $r2, $i, $i+1);
    }
    return $q;
}

function setNotesBM($q, $r1, $r2, $f, $s){
    $aux = 0;
    $s = $r1['exeruplo'];
    if($r1['exeruplo'] > $r2['exeruplo'] && $r1['totalmark'] === $r2['totalmark']){
        $q[$f]['notes'] = get_string('more_exercises_uploaded','league');
    }else{
        if($r1['totalmark'] === $r2['totalmark']){
            while (true) {
                if($s != $aux){
                    $n1 = $r1['marks'][$aux];
                    $n2 = $r2['marks'][$aux];
                    if($n1 && $n2){
                        if($n1 > $n2){
                            $q[$f]['notes'] = get_string('higher_mark','league').' '. comparaNotas($q, $f, $s, true).' a '
                                    . comparaNotas($q, $f, $s, false);
                            return $q;
                        }
                        if($n1 == $n2){
                            $aux += 1;
                        }
                    }else{
                        $q[$f]['notes'] = get_string('total_draw','league');
                        return $q;
                    }
                }else {
                    $q[$f]['notes'] = get_string('total_draw','league');
                    return $q;
                }
            }
        }
    }
    return $q;
}
function setNotesME($q, $r1, $r2, $f, $s){
    $aux = 0;
    $s = $r1['exeruplo'];
    if($r1['exeruplo'] != $r2['exeruplo']){
        if($r1['exeruplo'] > $r2['exeruplo']){
            $q[$f]['notes'] = get_string('more_exercises_uploaded','league');
        }
    }else{
        while (true) {
            if($s != $aux){
                $n1 = $r1['marks'][$aux];
                $n2 = $r2['marks'][$aux];
                if($n1 && $n2){
                    if($n1 > $n2){
                        $q[$f]['notes'] = get_string('higher_mark','league').' '. comparaNotas($q, $f, $s, true).' - '
                                . comparaNotas($q, $f, $s, false);
                        return $q;
                    }
                    if($n1 == $n2){
                        $aux += 1;
                    }
                }else{
                    $q[$f]['notes'] = get_string('total_draw','league');
                    return $q;
                }
            }else{
                $q[$f]['notes'] = get_string('total_draw','league');
                return $q;
            }
        }
    }
    return $q;
}

function comparaNotas($q, $i, $j, $primero){
    $notas1 = $q[$i]['marks'];
    $notas2 = $q[$j]['marks'];
    $i = 0;
    while (true) {
        $n1 = ($notas1[$i] ? $notas1[$i] : -1);
        $n2 = ($notas2[$i] ? $notas2[$i] : -1);
        if($n2 != $n1){
            return ($primero ? $n1 : $n2);
        }
        if($n1 == $n2){
            $i += 1;
        }
    }
}

// TRUE si r2 tiene mejores notas
function mejoresNotasSegundo($q, $r1, $r2){
    $i = 0;
    $s = $r1['exeruplo'];
    if($r1['exeruplo'] != $r2['exeruplo']){
        if($r1['exeruplo'] > $r2['exeruplo']){
            return false;
        }else{
            return true;
        }
    }else{
        while (true) {
            if($i != $s){
                $n1 = ($r1['marks'][$i] ? $r1['marks'][$i] : null);
                $n2 = ($r2['marks'][$i] ? $r2['marks'][$i] : null);
                if($n1 && $n2){
                    if($n2 > $n1){
                        return true;
                    }
                    if($n1 > $n2){
                        return false;
                    }
                    if($n1 == $n2){
                        $i += 1;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
}

function print_qualy($q, $rol = 'student', $iduser = -1){
    $pos = 1;
        ?>
<table id="qualy">
    <tr>
        <th><?= get_string('q_pos','league') ?></th>
        
        <?php
        if($rol == 'student'){
            ?> <th><?= get_string('q_name_hashed','league') ?></th> <?php
        }else{
            ?> <th><?= get_string('q_name','league') ?></th> <?php
        }
        ?>
        <?php if($rol == 'teacher'){ 
            echo "<th>".get_string('q_user','league')."</th>";
            echo "<th>".get_string('q_id','league')."</th>";
        } ?>
        <th><?= get_string('q_total_exercises','league') ?></th>
        <th><?= get_string('q_exercises_uploaded','league') ?></th>
        <th><?= get_string('q_total_mark','league') ?></th>
        <th><?= get_string('q_percentage','league') ?></th>
        <th><?= get_string('q_notes','league') ?></th>
        <?php if($rol == 'teacher' && $iduser != -1){ 
            echo '<th colspan="'.$q[0]['totalexer'].'">'.get_string('q_best_marks','league').'</th>';
        } ?>
        
    </tr>
        <?php
    foreach ($q as $r){
        $b = ($r['uid'] == $iduser);
        $i = $b;
        ?>
    <tr>
        <?php
        echo tdtable($pos, $b, $i);
        
        if($rol == 'teacher'){ 
            echo tdtable($r['name'], $b, $i);
            echo tdtable($r['uname'], $b, $i);
        } else if($rol == 'student'){ 
            if($r['uid'] == $iduser){
                echo tdtable($r['name'], $b, $i);
            } else {
                echo tdtable(md5($r['name']." - ".$r['uname']), $b, $i);
            }
        } 
        if($rol == 'teacher'){
            echo tdtable($r['uid'], $b, $i);
        }
        echo tdtable($r['totalexer'], $b, $i);
        echo tdtable($r['exeruplo'], $b, $i);
        echo tdtable($r['totalmark'], $b, $i);
        echo tdtable(($r['totalexer'] > 0 ? number_format(($r['totalmark'] / ($r['totalexer'] * 100)) * 100, 2, ',', ' ') . ' %' : 'NaN'), $b, $i);
        echo tdtable($r['notes'], $b, $i);
        if($rol === 'teacher' && $iduser != -1){
            foreach ($r['marks'] as $n){
                if($n){
                    echo tdtable($n, $b, $i);
                }
            }
        }
        ?>
    </tr>

        <?php
        $pos += 1;
    }
    ?> </table> <?php
}

function tdtable($content, $bold = false, $italic = false){
    $ret = '<td>';
    if($bold){
        $ret .= '<b>';
    }
    if($italic){
        $ret .= '<i>';
    }
    $ret .= $content;
    if($italic){
        $ret .= '</i>';
    }
    if($bold){
        $ret .= '</b>';
    }
    $ret .= '</td>';
    return $ret;
}

function getArrayMarkByStudent($idleague, $iduser, $toprint){
    global $DB;
    //Lista de estudiantes de un curso
    $var="select a.id, b.mark, a.published
            from mdl_league_exercise as a
            left outer join
            (
                select a.id as idat, a.timemodified as tma,
                        a.observations, a.name as fname,
                        a.exercise, b.id_user, a.mark, a.id_file, a.url
                        from mdl_league_attempt as a
                        inner join (
                                select max(id) as m, id_user
                                from mdl_league_attempt
                                where id_user = $iduser
                                group by exercise
                        ) as b
                        on a.id = b.m
            ) as b
            on a.id = b.exercise
            where a.league = $idleague
    order by mark desc";
    $data = $DB->get_records_sql($var);
    $mark = Array();
    foreach ($data as $d){
        $d = get_object_vars($d);
        if ($d['mark'] != -1){
            if($toprint || $d['published'] == 1){
                array_push($mark, $d['mark']);
            }
        }else{
            if($toprint){
                array_push($mark, get_string('q_tba','league'));
            }
        }
    }
    return $mark;
}

function publishedMarks($exercise){
    global $DB;
    //Lista de estudiantes de un curso
    $var="select a.published
        from mdl_league_exercise as a
        where id = $exercise";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        if ($d['published'] == 0){
            return false;
        }else{
            return true;
        }
    }
}

function getURLFile($contextid, $component, $filearea, $itemid, $name){
    global $CFG;
    
    $url = $CFG->wwwroot;
    $url .= "/pluginfile.php/";
    $url .= ($contextid)."/";
    $url .= ($component)."/";
    $url .= ($filearea)."/";
    $url .= ($itemid)."/";
    $url .= $name;
    return $url;
}


function restoreURLFile($contextid, $itemid){
    $component = 'mod_league';
    $filearea = 'exuplod';
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
        foreach ($files as $file) {
            $contenthash = $file->get_contenthash();
            $id_file = getIDFileFromContenthash($contenthash);


            $url = getURLFile($file->get_contextid(), $file->get_component(), 
                    $file->get_filearea(), $file->get_itemid(), $file->get_filename());

            $resultado = new stdClass();
            $resultado->id = $id_file;
            $resultado->url = $url;
            return $resultado;
        }
    }
    return null;
}
/*
function deleteFileAttempt($contextid, $itemid){
    $component = 'mod_league';
    $filearea = 'exuplod';
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'sortorder', false)) {               
        foreach ($files as $file) {
            $file->delete();
            return true;
        }
    }
    return false;
}*/

function get_id_and_user_attempt_from_itemid($itemid){
    global $DB;
    $res = new stdClass();
    /*$var="select id, id_user, league
        from mdl_league_attempt
        where id_file = $itemid";
    $data = $DB->get_records_sql($var);
    foreach ($data as $d){
        $d = get_object_vars($d);
        $res['id'] = $d['id'];
        $res['id_user'] = $d['id_user'];
        $res['league'] = $d['league'];
        return $res;
    }
     * 
     */
    $res['id'] = 16;
    $res['id_user'] = 3;
    $res['league'] = 8;
    return $res;
}