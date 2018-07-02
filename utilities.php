<?php

function print_exercises($idliga, $rol, $cmid){
    global $DB;
    $var="SELECT * 
    FROM mdl_exercise
    WHERE league = $idliga
    ORDER BY id";
    $data = $DB->get_records_sql($var);
    
    if ($rol == 'teacher'){
    
    ?>

<table border="1">
    <tr>
        <td><?= get_string('exercise', 'league') ?></td>
        <td><?= get_string('timemofied', 'league') ?></td>
        <td><?= get_string('enabled', 'league') ?></td>
    </tr>
    
    <?php
    foreach ($data as $exer)
    {
        $exer = json_decode(json_encode($exer), True);
        //print_r($exer);
        ?>
    <tr>
        <td><?= $exer['name'] ?></td>
        <td><?= date("H:i:s, d (D) M Y", $exer['timemodified']) ?></td>
        <td><?= ($exer['enabled'] == 0 ? get_string('no','league') : "<i><strong>".get_string('yes','league')."</strong></i>") ?></td>
        <td>
            <form action="management.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="exer_name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="exer_description" value="<?= $exer['statement'] ?>" />
                <input type="hidden" name="exer_enabled" value="<?= $exer['enabled'] ?>" />
                <input type="image" name="submit_red"  value="red"  alt="red " src="images/delete.png" width="20" height="20">
            </form>
            
            
        </td>
        <td><form action="add_exercise.php" method="get" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="statement" value="<?= $exer['statement'] ?>" />
                <input type="submit" value="<?= get_string('modify_exercise_button', 'league') ?>"/>
            </form>
        </td>
        <td><form action="management.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="action" value="enable_disable" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="exer_name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="exer_description" value="<?= $exer['statement'] ?>" />
                <input type="hidden" name="exer_enabled" value="<?= $exer['enabled'] ?>" />
                <input type="submit" value="<?= 
                ($exer['enabled'] == 0 ? get_string('enable_exercise_button', 'league') : get_string('disable_exercise_button', 'league')) 
            ?>"/>
            </form>
        </td><td><form action="marking.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="name" value="<?= $exer['name'] ?>" />
                <input type="submit" value="<?= get_string('mark_exercise', 'league') ?>"/>
            </form>
        </td>
    </tr>
        <?php
    }
    
?>
</table>
<?php

    } else if ($rol == 'student'){
        
        ?>

<table border="1">
    <tr>
        <td><?= get_string('exercise', 'league') ?></td>
        <td><?= get_string('timemofied', 'league') ?></td>
        <td><?= get_string('send_exercise', 'league') ?></td>
    </tr>
    
    <?php
    foreach ($data as $exer)
    {
        $exer = json_decode(json_encode($exer), True);
        
        if($exer['enabled'] == 1){
        
        ?>
    <tr>
        <td><?= $exer['name'] ?></td>
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

function print_students_exercise($cmid, $id_exer, $name){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_attempt as a
    inner join (
            select max(c.id) as id, c.id_user, d.firstname, d.lastname
            from mdl_attempt as c
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

<table border="1">
    <tr>
        <td><?= get_string('student', 'league') ?></td>
        <td><?= get_string('upload_time', 'league') ?></td>
        <td><?= get_string('mark', 'league') ?></td>
    </tr>
    
    <?php
    
    foreach ($data as $d){
        $d = get_object_vars($d);
        ?> <tr> 
            <td><?php echo $d['firstname']." ".$d['lastname']; ?></td>
            <td><?= date("H:i:s, d (D) M Y", $d['timemodified']) ?></td>
            <td><?php 
            if($d['mark'] == -1){
                echo get_string('no_mark_yet', 'league');
            }else{
                echo $d['mark']."%";
            }
            ?></td>
            <td>
                <a href="<?= $d['url'] ?>"><?= get_string('download_file_button', 'league') ?></a>
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

function print_notas_alumno($idleague, $cmid, $userid){
    global $DB;
    //Lista de ejercicios subidos por los alumnos (solo uno por alumno, ordenado por más reciente)
    $var="select *
    from mdl_exercise as a
    left outer join
    (
        select a.id as idat, a.timemodified as tma,
		a.observations, a.course as ca, a.name as fname,
		a.exercise, b.id_user, a.mark, a.id_file, a.url
		from mdl_attempt as a
		inner join (
			select max(id) as m, id_user
			from mdl_attempt
			where id_user = $userid
			group by exercise
		) as b
		on a.id = b.m
    ) as b
    on a.id = b.exercise
    where a.league = $idleague";
    $data = $DB->get_records_sql($var);
    ?>

<table border="1">
    <tr>
        <td><?= get_string('exercise', 'league') ?></td>
        <td><?= get_string('upload_time', 'league') ?></td>
        <td><?= get_string('file_uploaded', 'league') ?></td>
        <td><?= get_string('mark', 'league') ?></td>
        <td><?= get_string('reviews', 'league') ?></td>
    </tr>
    
    <?php
    
    foreach ($data as $d){
        $d = get_object_vars($d);
        if($d['enabled'] == 1 || $d['idat']){
        
        
        ?> <tr> 
            <td><?= $d['name'] ?></td>
            <td><?= ($d['tma'] ? date("H:i:s, d (D) M Y", $d['tma']) : "") ?></td>
            <td>
                <?php
                    if($d['url']){
                        echo "<a href=".$d['url'].">".get_string('download_file_button', 'league')."</a>";
                    }
                ?>
            </td>
            <td><?php 
            if($d['mark'] == -1){
                echo get_string('no_mark_yet', 'league');
            }else{
                if($d['mark']){
                    echo $d['mark']."%";
                }else{
                    echo "<b><i>".get_string('not_sent_yet', 'league')."</i></b>";
                }
            }
            ?></td>
            <td><?= $d['observations'] ?></td>
        
        </tr>
        <?php
        }
    }
    ?>
</table>

<?php
}

function get_qualy_array($idleague, $idcurso){
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
        from mdl_exercise as a
        left outer join
        (
            select a.id as idat, a.timemodified as tma,
                    a.observations, a.course as ca, a.name as fname,
                    a.exercise, b.id_user, a.mark, a.id_file, a.url
                    from mdl_attempt as a
                    inner join (
                            select max(id) as m, id_user
                            from mdl_attempt
                            where id_user = ${d['userid']}
                            group by exercise
                    ) as b
                    on a.id = b.m
        ) as b
        on a.id = b.exercise
        where a.league = $idleague";
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
            $fila += array('marks' => getArrayMarkByStudent($idleague, $d['userid']));
            $fila += array('notes' => "");
        }
        array_push($q, $fila);
    }
    return sort_qualy_array($q);
}

function exchange($array, $id1, $id2){
    $aux = $array[$id1];
    $array[$id1] = $array[$id2];
    $array[$id2] = $aux;
    return $array;
}

function sort_qualy_array($q){
   // print_r($q);
    $n = sizeof($q);
    //Algoritmo burbuja
    for ($i = 1; $i < $n; $i++){
        for($j = 0; $j < $n - $i; $j++){
            $r1 = $q[$j];
            $r2 = $q[$j+1];
         /*   echo "<br>--- LAP ---<br>";
            echo "<br> r1: ($i) <br>";
            print_r($r1);
            echo "<br> r2: ($j) <br>";
            print_r($r2);
         */
         /*   echo "<br>--- LAP ---<br>";
            echo "<br> r1: (${r1['totalmark']}) / j: $j <br>";
            echo "<br> r2: (${r2['totalmark']}) / j+1: ".($j+1)." <br>";
          */  
            if($r2['totalmark'] > $r1['totalmark']){
            //    echo "<br> CAMBIO <br>";
                //echo "<br> Antes: <br>";
                //print_r($);
                $q = exchange($q, $i, $j);
            }else if($r2['totalmark'] === $r1['totalmark']){
                $ex = mejoresNotasSegundo($q, $r1, $r2, $i, $j);
                switch ($ex) {
                    case 0:
                        $q[$i]['notes'] = "Empate Total";
                        $q[$j]['notes'] = "Empate Total";
                        break;
                    case 1:
                        $q[$i]['notes'] = "Ha subido menos ejercicios";
                        $q[$j]['notes'] = "Ha subido más ejercicios";
                        break;
                    case 2:
                        $q[$i]['notes'] = "Ha subido menos ejercicios";
                        $q[$j]['notes'] = "Ha subido más ejercicios";
                        break;
                    case 3:
                        $q[$i]['notes'] = "Ha obtenido mayor nota comparando ejercicios ("
                            .comparaNotas($q, $i, $j, true).")";
                        $q[$j]['notes'] = "Ha obtenido menor nota comparando ejercicios ("
                            .comparaNotas($q, $i, $j, false).")";
                        break;
                    case 4:
                        $q[$j]['notes'] = "Ha obtenido mayor nota comparando ejercicios ("
                            .comparaNotas($q, $i, $j, true).")";
                        $q[$i]['notes'] = "Ha obtenido menor nota comparando ejercicios ("
                            .comparaNotas($q, $i, $j, false).")";
                        break;
                    default:
                        break;
                }
                if ($ex === 1 || $ex === 3){
                    $q = exchange($q, $i, $j);
                }
            }else{
                $q[$i]['notes'] = "";
                $q[$j]['notes'] = "";
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
function mejoresNotasSegundo($q, $r1, $r2, $i, $j){
    $i = 0;
    if($r1['exeruplo'] != $r2['exeruplo']){
        if($r1['exeruplo'] > $r2['exeruplo']){
            return 2;
        }else{
            return 1;
        }
    }else{
        while (true) {
            $n1 = $r1['marks'][$i];
            $n2 = $r2['marks'][$i];
            echo "<br> $n1 - $n2 <br>";
            if($n1 && $n2){
                if($n2 > $n1){
                    return 3;
                    return true;
                }
                if($n1 > $n2){
                    return 4;
                    return false;
                }
                if($n1 == $n2){
                    $i += 1;
                }
            }else{
                return 0;
            }
        }
    }
}

function print_qualy($idleague, $idcurso, $iduser = -1){
    $q = get_qualy_array($idleague, $idcurso);
    $pos = 1;
        ?>
<table border="1">
    <tr>
        <td>POS</td>
        <td>Nombre</td>
        <td>Usuario</td>
        <td>ID</td>
        <td>TE</td>
        <td>ES</td>
        <td>NT</td>
        <td>PERC</td>
        <td>Notas</td>
    </tr>
        <?php
    foreach ($q as $r){
        ?>
    <tr>
        <td><?= $pos ?></td>
        <td><?= $r['name'] ?></td>
        <td><?= $r['uname'] ?></td>
        <td><?= $r['uid'] ?></td>
        <td><?= $r['totalexer'] ?></td>
        <td><?= $r['exeruplo'] ?></td>
        <td><?= $r['totalmark'] ?></td>
        <td><?= number_format(($r['totalmark'] / ($r['totalexer'] * 100)) * 100, 2, ',', ' ') ?> %</td>
        <td><?= ($r['notes'] ? $r['notes'] : "") ?></td>
    </tr>

        <?php
        $pos += 1;
    }
    ?> </table> <?php
}

function getArrayMarkByStudent($idleague, $iduser){
    global $DB;
    //Lista de estudiantes de un curso
    $var="select a.id, b.mark
            from mdl_exercise as a
            left outer join
            (
                select a.id as idat, a.timemodified as tma,
                        a.observations, a.course as ca, a.name as fname,
                        a.exercise, b.id_user, a.mark, a.id_file, a.url
                        from mdl_attempt as a
                        inner join (
                                select max(id) as m, id_user
                                from mdl_attempt
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
        array_push($mark, $d['mark']);
    }
    return $mark;
}