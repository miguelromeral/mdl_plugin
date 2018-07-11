<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class attempt_graded extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_attempt';
        $this->data['crud'] = 'u'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    
    public static function get_name() {
        return get_string('eventattemptgraded', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' updated an attempt with id '"
            .$this->objectid."' with a mark of '".$this->other['mark']."' for exercise with "
                . "id '".$this->other['exercise']."' with course module id '$this->contextinstanceid'.";
    }
}