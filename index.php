<?php

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
 
//admin_externalpage_setup('league');
 
// Set up the page.
$title = get_string('pluginname', 'league');
$pagetitle = $title;
$url = new moodle_url("/admin/tool/league/index.php");
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
 
$output = $PAGE->get_renderer('league');
 
echo $output->header();
echo $output->heading($pagetitle);
 
//$renderable = new \tool_demo\output\index_page('Some text');
//echo $output->render($renderable);
 
echo $output->footer();