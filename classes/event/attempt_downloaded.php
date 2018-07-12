<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class attempt_downloaded extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_attempt';
        $this->data['crud'] = 'r'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    
    public static function get_name() {
        return get_string('eventattemptdownloaded', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' downloaded an attempt with id '"
            .$this->objectid."' for the league with '"
                . "id '".$this->other['league']."' with course module id '$this->contextinstanceid'.";
    }
}