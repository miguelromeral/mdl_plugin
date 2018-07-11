<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class exercise_deleted extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_exercise';
        $this->data['crud'] = 'd'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    
    public static function get_name() {
        return get_string('eventexercisedeleted', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' deleted an exercise "
                . "with id '$this->objectid' with course module id '$this->contextinstanceid'.";
    }
}