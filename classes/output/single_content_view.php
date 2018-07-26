<?php

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');


class single_content_view implements \renderable {
 
    public function __construct($content, $title = null) {
        $this->title = $title;
        $this->content = $content;
    }
}