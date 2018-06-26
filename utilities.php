<?php

function print_exercises($idliga, $rol, $cmid){
    global $DB;
    $var="SELECT * 
    FROM mdl_exercise
    WHERE league = $idliga
    ORDER BY id";
    $data = $DB->get_records_sql($var);
    $num = 1;
    
    if ($rol == 'teacher'){
    
    ?>

<table border="1">
    <tr>
        <td>#</td>
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
        <td><?= $num ?></td>
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
        </td><td><form action="marking.php" method="get" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="name" value="<?= $exer['name'] ?>" />
                <input type="submit" value="<?= get_string('mark_exercise', 'league') ?>"/>
            </form>
        </td>
    </tr>
        <?php
        $num += 1;
    }
    
?>
</table>
<?php

    } else if ($rol == 'student'){
        
        ?>

<table border="1">
    <tr>
        <td>#</td>
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
        <td><?= $num ?></td>
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
        $num += 1;
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
            select c.id, c.id_user, d.firstname, d.lastname
            from mdl_attempt as c
            inner join mdl_user as d
            on c.id_user = d.id
            where c.exercise = $id_exer
            order by c.id desc
            limit 1
    ) as b
    on a.id = b.id";
    $data = $DB->get_records_sql($var);
    ?>

<h1><?= $name ?></h1>

<table border="1">
    <tr>
        <td><?= get_string('student', 'league') ?></td>
        <td><?= get_string('upload_time', 'league') ?></td>
        <td><?= get_string('mark', 'league') ?></td>
        <td><?= get_string('reviews', 'league') ?></td>
        <td><?= get_string('download_file', 'league') ?></td>
        <td><?= get_string('to_mark', 'league') ?></td>
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
                echo $d['mark'];
            }
            ?></td>
            <td>TBA</td>
            <td>
             <form action="download.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
                <input type="hidden" name="file" value="<?= $d['name'] ?>" />
                <input type="submit" value="<?= get_string('download_file_button', 'league') ?>"/>
            </form></td>
            <td>
             <form action="mark_student.php" method="post" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $id_exer ?>" />
                <input type="hidden" name="name" value="<?= $name ?>" />
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
