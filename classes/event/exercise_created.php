<?php

namespace mod_league\event;
defined('MOODLE_INTERNAL') || die();

class exercise_created extends \core\event\base {
    
    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'league_exercise';
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    
    public static function get_objectid_mapping() {
        return array('db' => 'league_exercise', 'restore' => 'league_exercise');
    }
    
    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['league'] = array('db' => 'league', 'restore' => 'league');
        return $othermapped;
    }
    
    public static function get_name() {
        return get_string('eventexercisecreated', 'league');
    }
    
    public function get_description() {
        return "The user with id '$this->userid' created an exercise "
                . "with id '$this->objectid' for league with id "
                . "'".$this->other['league']."' with course module id "
                . "'$this->contextinstanceid'.";
    }
}