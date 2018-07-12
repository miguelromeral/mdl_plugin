<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class attempt_submitted extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_attempt';
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    
    public static function get_objectid_mapping() {
        return array('db' => 'league_attempt', 'restore' => 'league_attempt');
    }
    
    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['exercise'] = array('db' => 'league_exercise', 'restore' => 'league_exercise');
        return $othermapped;
    }
    
    public static function get_name() {
        return get_string('eventattemptsubmitted', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' submitted an attempt "
                . "with id '$this->objectid' for exercise with id "
                . "'".$this->other['exercise']."' with course module id '$this->contextinstanceid'.";
    }
}

