<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
require_once('render_utilities.php');

class main_student_view implements renderable {
 
    public function __construct($presentation, $exercises, $cmid, $contextid, $alert = null, $notas = null,
            $candownload = false, $canupload = false) {
        if($presentation) {
            $this->presentation = $presentation;
        }
        if($exercises) {
            $this->exercises = $exercises;
        }
        $this->cmid = $cmid;
        $this->contextid = $contextid;
        $this->alert = $alert;
        $this->notas = $notas;
        $this->canupload = $canupload;
        $this->candownload = $candownload;
        if($alert == 'grades'){
            $this->title = get_string('my_marks','league');
            $this->exercises_title = null;
            $this->marks_title = null;
        }else{
            $this->title = get_string('main_panel_student','league');
            $this->exercises_title = get_string('availables_exercises','league');
            $this->marks_title = get_string('my_marks','league');
        }
    }
}

class main_teacher_view implements renderable {
 
    public function __construct($exercises, $cmid, $contextid, $canmark = false, $alert = null) {
        $this->exercises = $exercises;
        $this->cmid = $cmid;
        $this->contextid = $contextid;
        $this->canmark = $canmark;
        $this->alert = $alert;
        $this->title = get_string('teacher_panel','league');
        $this->exercises_title = get_string('h_manag_exer','league');
        
    }
}


class total_attempts_view implements renderable {
 
    public function __construct($cmid, $attempts, $idexer, $name, $contextid) {
        $this->cmid = $cmid;
        $this->idexer = $idexer;
        $this->attempts = $attempts;
        $this->name = $name;
        $this->contextid = $contextid;
    }
}

class grade_view implements renderable {
 
    public function __construct($rows, $tablecolumns, $tableheaders, $ex_name, $url) {
        $this->rows = $rows;
        $this->tablecolumns = $tablecolumns;
        $this->tableheaders = $tableheaders;
        $this->ex_name = $ex_name;
        $this->url = $url;
    }
}

class go_back_view implements renderable {
 
    public function __construct($title, $content, $cmid, $page, $hidden = null) {
        $this->title = $title;
        $this->content = $content;
        $this->cmid = $cmid;
        $this->page = $page;
        $this->hidden = $hidden;
    }
}

class single_content_view implements renderable {
 
    public function __construct($content, $title = null) {
        $this->title = $title;
        $this->content = $content;
    }
}

class mod_league_renderer extends plugin_renderer_base {
 
    protected function render_main_student_view(\main_student_view $view) {
        $image = '<img src="pix/animated.gif" width="40" height="40"/>';
        $out = $this->output->heading($image . format_string($view->title), 2);
        if($view->alert != 'grades'){
            $out  .= $this->output->container($view->presentation);
            $out  .= $this->output->heading(format_string($view->exercises_title), 3);
            if(isset($view->exercises)){
                $out  .= $this->output->container(print_exercises('student', $view->cmid,
                    $view->exercises, $view->canupload));
            }
            $out  .= $this->output->heading(format_string($view->marks_title), 3);
        }
        $out  .= $this->output->container(print_notas_alumno($view->notas, $view->contextid, $view->candownload));
        return $this->output->container($out, 'main');
    }
    
    protected function render_main_teacher_view(\main_teacher_view $view) {
        $out = '';
        $image = '<img src="pix/animated.gif" width="40" height="40"/>';
        $out  = $this->output->heading($image . format_string($view->title), 2);
        $out  .= $this->output->container(print_exercises('teacher', $view->cmid,
                $view->exercises, false, $view->canmark));
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
                    <input type="hidden" name="exercise" value="-1" />
                    <input type="submit" value="'. get_string('add_exercise_button', 'league') .'"/>
                </form>';
        $out  .= $this->output->container($button, 'button');
        
        return $this->output->container($out, 'main');
    }
    
    protected function render_qualy_view(\mod_league\output\qualy_view $view) {
        //$image = '<img src="pix/animated.gif" width="40" height="40"/>';
        //$out = $this->output->heading($image . format_string($view->title), 2);
        $out = $this->output->heading(format_string($view->title), 2);
        $out  .= $this->output->container($view->print_qualy());
        
        // Explanation about the heading columns.
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
        
        return $this->output->container($out, 'main');
    }
    
    protected function render_total_attempts_view(\total_attempts_view $view) {
        $out = $this->output->heading(format_string($view->name), 2);
        $out .= $this->output->container(
                print_attempts_exercise($view->attempts, $view->cmid, $view->idexer, 
                        $view->name, $view->contextid));
        
        $button = '<form action="view.php" method="get">
                    <input type="hidden" name="id" value="'. $view->cmid .'" />
                    <input type="submit" value="'. get_string('go_back', 'league') .'"/>
                </form>';
            $out  .= $this->output->container($button, 'button');
        return $this->output->container($out, 'main');
    }
    
    protected function render_grade_view(\grade_view $view) {
        //$out = $this->output->heading(format_string(get_string('individual_marks', 'league')), 2);
        $out = $this->output->container(print_table_grades($view->rows, $view->tablecolumns, $view->tableheaders, $view->ex_name, $view->url));
        return $this->output->container($out, 'main');
    }
    
    protected function render_single_content_view(\single_content_view $view) {
        $out = '';
        if($view->title){
            $out .= $this->output->heading($view->title, 2);
        }
        $out .= $this->output->container($view->content);
        return $this->output->container($out, 'main');
    }
    
    protected function render_go_back_view(\go_back_view $view) {
        $out = $this->output->heading(format_string($view->title), 2);
        $out .= $this->output->container($view->content);
        $button = '<form action="'.$view->page.'" method="get">
                    <input type="hidden" name="id" value="'. $view->cmid .'" />';
        
        if($view->hidden){
            foreach($view->hidden as $k => $v){
                $button .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
            }
        }
        $button .= '<input type="submit" value="'. get_string('go_back', 'league') .'"/>
                </form>';
        $out  .= $this->output->container($button, 'button');
        return $this->output->container($out, 'main');
    }
}