<?php

//  If you add or change observers you need to purge the caches or they will not be recognised.
//  Plugin developers need to bump up the version number to guarantee that the list
//  of observers is reloaded during upgrade.

$observers = array(
 
    array(
        // fully qualified event class name or "*" indicating all events
        'eventname'   => '\mod_league\event\league_created',
        // PHP callable type.
        'callback'    => 'core_event_sample_observer::observe_one',
        // optional. File to be included before calling the observer. Path relative to dirroot.
        //'includefile' => null,
        // optional. Defaults to true. Non-internal observers are not called 
        // during database transactions, but instead after a successful commit of the transaction.
        //'internal'    => true,
        // optional. Defaults to 0. Observers with higher priority are notified first.
        //'priority'    => 9999,
    )
    
);
