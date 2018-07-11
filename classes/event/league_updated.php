<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class league_updated extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league';
        $this->data['crud'] = 'u'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    
    public static function get_name() {
        return get_string('eventleagueupdated', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' updated a league "
                . "with id '$this->objectid' with course module id '$this->contextinstanceid'.";
    }
}