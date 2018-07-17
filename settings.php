<?php


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    
    require_once($CFG->dirroot.'/mod/league/lib.php');

    $options = array();
    $options[1] = get_string('only_one_attempt', 'league');
    for($i = 2; $i <= 20; $i++){
        $options[$i] = $i;
    }
    
    $settings->add(new admin_setting_configselect('league_max_num_attempts',
            get_string('max_num_attempts', 'league'),
            get_string('max_num_attempts_desc', 'league'),
            5, $options));
    
}

