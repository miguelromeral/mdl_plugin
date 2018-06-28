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