<?php

namespace mod_league;
 
class league {
    private $cm;
    private $context;
 
    function __construct(\cm_info $cm, $context_module) {
        $this->cm = $cm;
        $this->context = $context_module;
    }
    
    public function userview($userid = null){
        return (has_capability('mod/league:view', $this->context, $userid));
    }
    
    public function usermanageexercises($userid = null){
        return (has_capability('mod/league:manageexercises', $this->context, $userid));
    }
    
    public function userseequaly($userid = null){
        return (has_capability('mod/league:seequaly', $this->context, $userid));
    }
    
    public function userseerestrictedqualy($userid = null){
        return (has_capability('mod/league:seerestrictedqualy', $this->context, $userid));
    }
    
    public function userdownloadfiles($userid = null){
        return (has_capability('mod/league:downloadfiles', $this->context, $userid));
    }
    
    public function useruploadfiles($userid = null){
        return (has_capability('mod/league:uploadfiles', $this->context, $userid));
    }
    
    public function usermarkstudents($userid = null){
        return (has_capability('mod/league:markstudents', $this->context, $userid));
    }
}