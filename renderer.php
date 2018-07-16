<?php

defined('MOODLE_INTERNAL') || die();
require_once('render_utilities.php');

class main_view implements renderable {
 
    public function __construct($exercises, $cmid, $contextid, $rol = 'student', $alert = null, $notas = null) {
        $this->exercises = $exercises;
        $this->cmid = $cmid;
        $this->contextid = $contextid;
        $this->role = $rol;
        if($rol == 'student'){
            $this->notas = $notas;
            $this->title = get_string('main_panel_student','league');
            $this->exercises_title = get_string('availables_exercises','league');
            $this->marks_title = get_string('my_marks','league');
        }else if($rol == 'teacher'){
            $this->title = get_string('main_panel_student','league');
            $this->alert = $alert;
            $this->exercises_title = get_string('h_manag_exer','league');
        }
    }
}
 
class mod_league_renderer extends plugin_renderer_base {
 
    protected function render_main_view(\main_view $view) {
        $out = '';
        $image = '<img src="images/animated.gif" width="40" height="40"/>';
        if($view->role == 'student'){
            $out  = $this->output->heading($image . format_string($view->title), 2);
            $out  .= $this->output->heading(format_string($view->exercises_title), 3);
            $out  .= $this->output->container(print_exercises($view->role, $view->cmid,
                    $view->exercises));
            $out  .= $this->output->heading(format_string($view->marks_title), 3);
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
}