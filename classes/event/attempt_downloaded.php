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
    
    public static function get_objectid_mapping() {
        return array('db' => 'league_attempt', 'restore' => 'league_attempt');
    }
    
    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['league'] = array('db' => 'league', 'restore' => 'league');
        $othermapped['exercise'] = array('db' => 'league_exercise', 'restore' => 'league_exercise');
        return $othermapped;
    }
    
    public static function get_name() {
        return get_string('eventattemptdownloaded', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' downloaded an attempt with id '"
            .$this->objectid."' for the exercise with id '".$this->other['exercise']."' in the league with '"
                . "id '".$this->other['league']."' with course module id '$this->contextinstanceid'.";
    }
}