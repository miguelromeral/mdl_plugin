<?php

/*
 * OJO, ESTE ES EL EJEMPLO QUE PONEN, HABRÁ QUE EDITARLO
 */

$capabilities = array(
 
    'mod/league:addinstance' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),
    
    'mod/league:view' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'archetypes' => array(
        'guest' => CAP_ALLOW,
        'student' => CAP_ALLOW,
        'teacher' => CAP_ALLOW,
        'editingteacher' => CAP_ALLOW,
        'manager' => CAP_ALLOW
        )
    )
);

