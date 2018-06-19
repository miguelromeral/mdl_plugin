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
);