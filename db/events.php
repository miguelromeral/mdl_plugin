<?php

//  If you add or change observers you need to purge the caches or they will not be recognised.
//  Plugin developers need to bump up the version number to guarantee that the list
//  of observers is reloaded during upgrade.

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        // fully qualified event class name or "*" indicating all events
        'eventname'   => '\mod_league\event\league_created',
        // PHP callable type.
        'callback'    => 'league_created_handler',
        // optional. File to be included before calling the observer. Path relative to dirroot.
        'includefile' => '/mod/league/locallib.php',
        // optional. Defaults to true. Non-internal observers are not called 
        // during database transactions, but instead after a successful commit of the transaction.
        'internal'    => true,
        // optional. Defaults to 0. Observers with higher priority are notified first.
        //'priority'    => 9999,
    ),
    
    array(
        'eventname'   => '\mod_league\event\league_updated',
        'callback'    => 'league_updated_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\exercise_created',
        'callback'    => 'exercise_created_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\exercise_updated',
        'callback'    => 'exercise_updated_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\exercise_deleted',
        'callback'    => 'exercise_deleted_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\attempt_submitted',
        'callback'    => 'attempt_submitted_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\attempt_downloaded',
        'callback'    => 'attempt_downloaded_handler',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    ),
    
    array(
        'eventname'   => '\mod_league\event\attempt_graded',
        'callback'    => '',
        'includefile' => '/mod/league/locallib.php',
        'internal'    => true,
    )
    
);
