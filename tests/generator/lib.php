<?php


defined('MOODLE_INTERNAL') || die();

class mod_league_generator extends testing_module_generator {

    public function create_instance($record = null, array $options = null) {
        global $CFG;

        $record = (object)(array)$record;

        $defaultleaguesettings = array(
            'method' => 1
        );

        foreach ($defaultleaguesettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }
}
