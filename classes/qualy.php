<?php

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/league/classes/model.php');

class league_qualy {
    
    private $leagueid = null;
    private $courseid = null;
    private $role = null;
    private $method = null;
    private $qualy = null;
    
    public function __construct($idleague, $idcurso, $rol, $method){
        $this->leagueid = $idleague;
        $this->courseid = $idcurso;
        $this->role = $rol;
        $this->method = $method;
        $this->qualy = $this->get_qualy_array();
    }
    
    public function get_qualy(){
        return $this->qualy;
    }
    
    private function get_qualy_array(){
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
          AND (ue.timeend = 0 OR ue.timeend > NOW()) AND ue.status = 0 and c.id = $this->courseid";
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
                        a.exercise, b.id_user, a.mark, a.id_file
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
            where a.league = $this->leagueid";
            if($this->role == 'student'){
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
                $fila += array('marks' => \league_model::getArrayMarkByStudent($this->leagueid, $d['userid'], true));
                $fila += array('notes' => "");
            }
            array_push($q, $fila);
        }

        switch($this->method){
            case 1: return $this->sort_qualy_array_best_marks($q);
            case 2: return $this->sort_qualy_array_more_exercises($q);
            default: return $q;
        }
    }
    
    
    private function sort_qualy_array_best_marks($q){
        $n = sizeof($q);
        //Algoritmo burbuja
        for ($i = 1; $i < $n; $i++){
            for($j = 0; $j < $n - $i; $j++){
                $r1 = $q[$j];
                $r2 = $q[$j+1];
                //echo "<br> Miro ".$j." y ".($j+1)." ( ${r1['totalmark']} / ${r2['totalmark']}) <br>";
                if($r2['totalmark'] > $r1['totalmark'] || 
                        ($r2['totalmark'] === $r1['totalmark'] && $this->mejoresNotasSegundo($q, $r1, $r2))){
                   // echo "<br>CAMBIO<br>";
                    $q = $this->exchange($q, $j, $j+1);
                }
            }
           }
        //Ya está ordenado, ahora a poner las aclaraciones en caso de empates
        for ($i = 0; $i < $n - 1; $i++){
            $r1 = $q[$i];
            $r2 = $q[$i+1];
            $q = $this->setNotesBM($q, $r1, $r2, $i, $i+1);
        }
        return $q;
    }

    private function sort_qualy_array_more_exercises($q){
        $n = sizeof($q);
        //Algoritmo burbuja
        for ($i = 1; $i < $n; $i++){
            for($j = 0; $j < $n - $i; $j++){
                $r1 = $q[$j];
                $r2 = $q[$j+1];
                //echo "<br> Miro ".$j." y ".($j+1)." ( ${r1['totalmark']} / ${r2['totalmark']}) <br>";
                if($r2['exeruplo'] > $r1['exeruplo'] ||
                        ($r2['exeruplo'] === $r1['exeruplo'] && 
                            ($r2['totalmark'] > $r1['totalmark']))){
                    //echo "<br>CAMBIO<br>";
                    $q = $this->exchange($q, $j, $j+1);
                }
            }
           }
        //Ya está ordenado, ahora a poner las aclaraciones en caso de empates
        for ($i = 0; $i < $n - 1; $i++){
            $r1 = $q[$i];
            $r2 = $q[$i+1];
            $q = $this->setNotesME($q, $r1, $r2, $i, $i+1);
        }
        return $q;
    }
    

    private function exchange($array, $id1, $id2){
        $aux = $array[$id1];
        $array[$id1] = $array[$id2];
        $array[$id2] = $aux;
        return $array;
    }


    private function setNotesBM($q, $r1, $r2, $f, $s){
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
                                $q[$f]['notes'] = get_string('higher_mark','league').' '. $this->comparaNotas($q, $f, $s, true).' a '
                                        . $this->comparaNotas($q, $f, $s, false);
                                return $q;
                            }
                            if($n1 == $n2){
                                $aux += 1;
                            }else{
                                if($n2 == get_string('q_tba', 'league')){
                                    //$q[$f]['notes'] = "REVISAR";
                                    return $q;
                                }
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
    
    private function setNotesME($q, $r1, $r2, $f, $s){

        $aux = 0;
        $s = $r1['exeruplo'];
        if($r1['exeruplo'] != $r2['exeruplo']){
            if($r1['exeruplo'] > $r2['exeruplo']){
                $q[$f]['notes'] = get_string('more_exercises_uploaded','league');
            }
        }else{
            if($r1['totalmark'] === $r2['totalmark']){
                while (true) {
                    if($s != $aux){
                        $n1 = $r1['marks'][$aux];
                        $n2 = $r2['marks'][$aux];
                        if($n1 && $n2){
                            if($n1 > $n2){
                                $q[$f]['notes'] = get_string('higher_mark','league').' '. $this->comparaNotas($q, $f, $s, true).' - '
                                        . $this->comparaNotas($q, $f, $s, false);
                                return $q;
                            }
                            if($n1 == $n2){
                                $aux += 1;
                            }else{
                                if($n2 == get_string('q_tba', 'league')){
                                    //$q[$f]['notes'] = "REVISAR";
                                    return $q;
                                }
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
        }
        return $q;
    }

    private function comparaNotas($q, $i, $j, $primero){
        $notas1 = $q[$i]['marks'];
        $notas2 = $q[$j]['marks'];
        //$ant1 = -1;
        //$ant2 = -1;
        $i = 0;
        while (true) {
            $n1 = (isset($notas1[$i]) ? $notas1[$i] : -1);
            $n2 = (isset($notas2[$i]) ? $notas2[$i] : -1);
            if($n2 != $n1){
                return ($primero ? $n1 : $n2);
            }
            if($n1 == $n2){
                if($n1 == -1){
                    return get_string('q_tba', 'league');
                    //return ($primero ? $ant1 : $ant2);
                }
                //$ant1 = $n1;
                //$ant2 = $n2;
                $i += 1;
            }
        }
    }

    // TRUE si r2 tiene mejores notas
    private function mejoresNotasSegundo($q, $r1, $r2){
        $i = 0;
        $s = max($r1['exeruplo'],$r2['exeruplo']);
        if($r1['exeruplo'] != $r2['exeruplo']){
            if($r2['exeruplo'] > $r1['exeruplo']){
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

}
    