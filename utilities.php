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
        <td>Ejercicio</td>
        <td>Fecha de modificación</td>
        <td>Habilitado</td>
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
                <input type="hidden" name="exer_name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="exer_description" value="<?= $exer['statement'] ?>" />
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
                <input type="hidden" name="exer_name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="exer_description" value="<?= $exer['statement'] ?>" />
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
        <td>Ejercicio</td>
        <td>Fecha de modificación</td>
        <td>Enviar ejercicio</td>
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
             <form action="upload.php" method="get" >
                <input type="hidden" name="id" value="<?= $cmid ?>" />
                <input type="hidden" name="id_exer" value="<?= $exer['id'] ?>" />
                <input type="hidden" name="exer_name" value="<?= $exer['name'] ?>" />
                <input type="hidden" name="exer_description" value="<?= $exer['statement'] ?>" />
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
