<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class exercise_updated extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_exercise';
        $this->data['crud'] = 'u'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    
    public static function get_name() {
        return get_string('eventexerciseupdated', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' updated an exercise "
                . "with id '$this->objectid' named '".$this->other['name']."' for league with id "
                . "'".$this->other['league']."' with course module id '$this->contextinstanceid'.";
    }
}