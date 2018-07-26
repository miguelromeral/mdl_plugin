<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');


class go_back_view implements \renderable {
 
    public $title = null;
    public $content = null;
    public $cmid = null;
    public $page = 'view.php';
    public $hidden = null;
    
    public function __construct($cmid, $title = null, $content = null, $page = 'view.php', $hidden = null) {
        $this->title = $title;
        $this->content = $content;
        $this->cmid = $cmid;
        $this->page = $page;
        $this->hidden = $hidden;
    }
}