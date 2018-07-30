<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the renderer for the league module.
 *
 * @package   mod_league
 * @copyright 2018 Miguel Romeral
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

/**
 * The renderer class for the league module.
 *
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_league_renderer extends plugin_renderer_base {
 
    /**
     * Render the enabled exercises to the students.
     * 
     * @param \mod_league\output\available_exercises_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_available_exercises_view(\mod_league\output\available_exercises_view $view) {
        $out = $this->output->heading(format_string(get_string('availables_exercises','league')), 3);
        
        if(isset($view->exercises)){
            $out  .= $this->output->container($view->print_exercises());
        }else{
            $out  .= $this->output->container(get_string('no_exercises_availables', 'league'));
        }
        
        return $this->output->container($out, 'main');
    }
 
    /**
     * Render the students grades for each exercise in the league.
     * 
     * @param \mod_league\output\student_grade_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_student_grade_view(\mod_league\output\student_grade_view $view) {
        $out = $this->output->heading(format_string(get_string('my_marks','league')), 3);
        
        // If there are marks, print the table.
        if(isset($view->marks)){
            $out  .= $this->output->container($view->print_grades());
        }else{
            $out  .= $this->output->container(get_string('no_grades_availables', 'league'));
        }
        
        return $this->output->container($out, 'main');
    }
    
    /**
     * Render all exercises created and its operations to the teacher.
     * 
     * @param \mod_league\output\main_teacher_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_main_teacher_view(\mod_league\output\main_teacher_view $view) {
        $out = $this->output->heading(format_string(get_string('teacher_panel','league')), 2);
        
        // Print the exercise table if there are.
        if($view->exercises){
            $out .= $this->output->container($view->print_exercises());
        }else{
            $out .= $this->output->container(get_string('no_exercises_created','league'));
        }
        
        $out .= $this->output->container($view->print_alert());
        $out .= $this->output->container($view->print_add_exercise_button(), 'button');
        
        return $this->output->container($out, 'main');
    }
    
    /**
     * Render the qualy view.
     * 
     * @param \mod_league\output\qualy_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_qualy_view(\mod_league\output\qualy_view $view) {
        
        $out = $this->output->heading(format_string($view->title), 2);
        $out .= $this->output->container($view->print_qualy());
        $out .= $view->print_qualy_explanation($this->output);
        
        return $this->output->container($out, 'main');
    }
    
    /**
     * Rebder a table with all the attempts to one exercise.
     * 
     * @param \mod_league\output\total_attempts_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_total_attempts_view(\mod_league\output\total_attempts_view $view) {
        $out = $this->output->heading(format_string($view->name), 2);
        
        // If there are attempts, print them.
        if($view->attempts_exist()){
            $out .= $this->output->container($view->print_attempts());
        }else{
            $out .= $this->output->container(get_string('no_attempts_yet','league'));
        }
        
        return $this->output->container($out, 'main');
    }
    
    /**
     * Render the individual grades for each user and exercise (only for teachers).
     * 
     * @param \mod_league\output\teacher_grade_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_teacher_grade_view(\mod_league\output\teacher_grade_view $view) {
        $out = $this->output->container($view->print_table_grades());
        return $this->output->container($out, 'main');
    }
    
    /**
     * Render a single content in the page.
     * 
     * @param \mod_league\output\single_content_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_single_content_view(\mod_league\output\single_content_view $view) {
        $out = '';
        
        // Sometimes a title isn't necessary.
        if($view->title){
            $out .= $this->output->heading($view->title, 2);
        }
        
        $out .= $this->output->container($view->content);
        return $this->output->container($out, 'main');
    }
    
    /**
     * Render a button to go back. Usefull to navigate about the plugin.
     * 
     * @param \mod_league\output\go_back_view $view View with all appropiate data.
     * @return string HTML output data.
     */
    protected function render_go_back_view(\mod_league\output\go_back_view $view) {
        $out = '';
        
        // It might need a title.
        if($view->title){
            $out = $this->output->heading(format_string($view->title), 2);
        }
        
        // It might need a content (a single line with info).
        if($view->content){
            $out .= $this->output->container($view->content);
        }
        
        // Create the button to go back.
        $button = '<form action="'.$view->page.'" method="get">
                    <input type="hidden" name="id" value="'. $view->cmid .'" />';
        
        // If there are some more parameters that we have to include on the button
        // we add right here.
        if($view->hidden){
            foreach($view->hidden as $k => $v){
                $button .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
            }
        }
        
        // Close the button.
        $button .= '<input type="submit" value="'. get_string('go_back', 'league') .'"/>
                </form>';
        
        $out  .= $this->output->container($button, 'button');
        return $this->output->container($out, 'main');
    }
}