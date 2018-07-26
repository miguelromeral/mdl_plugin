<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
require_once('render_utilities.php');

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
 
    protected function render_available_exercises_view(\mod_league\output\available_exercises_view $view) {
        $out = $this->output->heading(format_string(get_string('availables_exercises','league')), 3);
        
        if(isset($view->exercises)){
            $out  .= $this->output->container($view->print_exercises());
        }else{
            $out  .= $this->output->container(get_string('no_exercises_availables', 'league'));
        }
        
        return $this->output->container($out, 'main');
    }
 
    protected function render_student_grade_view(\mod_league\output\student_grade_view $view) {
        $out = $this->output->heading(format_string(get_string('my_marks','league')), 3);
        
        if(isset($view->marks)){
            $out  .= $this->output->container($view->print_grades());
        }else{
            $out  .= $this->output->container(get_string('no_grades_availables', 'league'));
        }
        
        return $this->output->container($out, 'main');
    }
    
    protected function render_main_teacher_view(\mod_league\output\main_teacher_view $view) {
        $out = $this->output->heading(format_string(get_string('teacher_panel','league')), 2);
        $out .= $this->output->container($view->print_exercises());
        $out .= $this->output->container($view->print_alert());
        $out .= $this->output->container($view->print_add_exercise_button(), 'button');
        
        return $this->output->container($out, 'main');
    }
    
    protected function render_qualy_view(\mod_league\output\qualy_view $view) {
        
        $out = $this->output->heading(format_string($view->title), 2);
        $out .= $this->output->container($view->print_qualy());
        $out .= $view->print_qualy_explanation($this->output);
        
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