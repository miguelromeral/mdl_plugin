<?php

defined('MOODLE_INTERNAL') || die();
require_once('render_utilities.php');

class main_view implements renderable {
 
    public function __construct($exercises, $cmid, $contextid, $rol = 'student', $alert = null, $notas = null) {
        $this->exercises = $exercises;
        $this->cmid = $cmid;
        $this->contextid = $contextid;
        $this->role = $rol;
        $this->alert = $alert;
        if($rol == 'student'){
            $this->notas = $notas;
            if($alert == 'grades'){
                $this->title = get_string('my_marks','league');
                $this->exercises_title = null;
                $this->marks_title = null;
            }else{
                $this->title = get_string('main_panel_student','league');
                $this->exercises_title = get_string('availables_exercises','league');
                $this->marks_title = get_string('my_marks','league');
            }
        }else if($rol == 'teacher'){
            $this->title = get_string('teacher_panel','league');
            $this->exercises_title = get_string('h_manag_exer','league');
        }
    }
}

class qualy_view implements renderable {
 
    public function __construct($cmid, $qualy, $userid, $rol = 'student', $qualy_aux = null) {
        $this->role = $rol;
        $this->cmid = $cmid;
        $this->userid = $userid;
        $this->qualy = $qualy;
        $this->title = get_string('qualy_title', 'league');
        if($rol == 'teacher'){
            $this->qualy_aux = $qualy_aux;
        }
    }
}
 
class attempts_view implements renderable {
 
    public function __construct($cmid, $attempts, $idexer, $name, $contextid) {
        $this->cmid = $cmid;
        $this->idexer = $idexer;
        $this->attempts = $attempts;
        $this->name = $name;
        $this->contextid = $contextid;
    }
}

class grade_view implements renderable {
 
    public function __construct($exercises, $marks, $url) {
        $this->marks = $marks;
        $this->exercises = $exercises;
        $this->url = $url;
    }
}


class mod_league_renderer extends plugin_renderer_base {
 
    protected function render_main_view(\main_view $view) {
        $out = '';
        $image = '<img src="images/animated.gif" width="40" height="40"/>';
        if($view->role == 'student'){
            $out  = $this->output->heading($image . format_string($view->title), 2);
            if($view->alert != 'grades'){
                $out  .= $this->output->heading(format_string($view->exercises_title), 3);
                $out  .= $this->output->container(print_exercises($view->role, $view->cmid,
                        $view->exercises));
                $out  .= $this->output->heading(format_string($view->marks_title), 3);
            }
            $out  .= $this->output->container(print_notas_alumno($view->notas, $view->contextid));
        }else{
            $out  = $this->output->heading($image . format_string($view->title), 2);
            $out  .= $this->output->container(print_exercises($view->role, $view->cmid,
                    $view->exercises));
            if($view->alert){
                $msg = '<p><center>
                <strong>
                '.$view->alert.'
                </strong>
                </center></p>';
                $out  .= $this->output->container($msg, 'warning');
            }
            
            $button = '<form action="add_exercise.php" method="get">
                        <input type="hidden" name="id" value="'. $view->cmid .'" />
                        <input type="hidden" name="id_exer" value="-1" />
                        <input type="submit" value="'. get_string('add_exercise_button', 'league') .'"/>
                    </form>';
            $out  .= $this->output->container($button, 'button');
        }
        //$out .= $this->output->container(format_text('nada', FORMAT_HTML), 'content');
        //contianer --> Elemento y "class"
        return $this->output->container($out, 'main');
    }
    
    protected function render_qualy_view(\qualy_view $view) {
        $image = '<img src="images/animated.gif" width="40" height="40"/>';
        $out = $this->output->heading($image . format_string($view->title), 2);
        $out  .= $this->output->container(print_qualy($view->qualy, $view->role, $view->userid));
        if($view->role == 'teacher'){
            $out .= $this->output->heading(format_string(get_string('qts', 'league')), 3);
            $out  .= $this->output->container(print_qualy($view->qualy_aux, $view->role));
        }
        $out  .= $this->output->container(
                get_string('q_pos','league').': '.get_string('q_pos_des','league'));
        $out  .= $this->output->container(
                get_string('q_total_exercises','league').': '.get_string('q_total_exercises_des','league'));
        $out  .= $this->output->container(
                get_string('q_exercises_uploaded','league').': '.get_string('q_exercises_uploaded_des','league'));
        $out  .= $this->output->container(
                get_string('q_total_mark','league').': '.get_string('q_total_mark_des','league'));
        $out  .= $this->output->container(
                get_string('q_percentage','league').': '.get_string('q_percentage_des','league'));
        $out  .= $this->output->container(
                get_string('q_notes','league').': '.get_string('q_notes_des','league'));
        
        $button = '<form action="view.php" method="get">
                    <input type="hidden" name="id" value="'. $view->cmid .'" />
                    <input type="submit" value="'. get_string('go_back', 'league') .'"/>
                </form>';
            $out  .= $this->output->container($button, 'button');
        return $this->output->container($out, 'main');
    }
    
    protected function render_attempts_view(\attempts_view $view) {
        $out = $this->output->heading(format_string($view->name), 2);
        $out .= $this->output->container(
                print_students_exercise($view->attempts, $view->cmid, $view->idexer, 
                        $view->name, $view->contextid));
        
        $button = '<form action="view.php" method="get">
                    <input type="hidden" name="id" value="'. $view->cmid .'" />
                    <input type="submit" value="'. get_string('go_back', 'league') .'"/>
                </form>';
            $out  .= $this->output->container($button, 'button');
        return $this->output->container($out, 'main');
    }
    
    protected function render_grade_view(\grade_view $view) {
        $out = $this->output->heading(format_string(get_string('title_grade', 'league')), 2);
        $out .= $this->output->container(print_table_grades($view->exercises, $view->marks, $view->url));
        return $this->output->container($out, 'main');
    }
}