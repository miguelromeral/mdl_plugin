<?php

namespace mod_league;
 
class league {
    private $cm;
    private $context;
 
    function __construct(\cm_info $cm) {
        $this->cm = $cm;
        $this->context = context_module::instance($cm->id);
    }
    
    public function userViewPermission($userid = null){
        return (has_capability('mod/league:view', $this->context, $userid));
    }
}