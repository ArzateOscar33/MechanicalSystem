<?php
// Config/Apis.php

// ===== Secomext =====
defined('SECOMEXT_USER')         || define('SECOMEXT_USER', 'mechemission2');
defined('SECOMEXT_PASS')         || define('SECOMEXT_PASS', 'AFf0qvB38ffa$');
defined('SECOMEXT_WORKSHOP_ID')  || define('SECOMEXT_WORKSHOP_ID', 62);
defined('SECOMEXT_EQUIPMENT_ID') || define('SECOMEXT_EQUIPMENT_ID', '1');
defined('SECOMEXT_INSPECTOR_ID') || define('SECOMEXT_INSPECTOR_ID', '1');
defined('SECOMEXT_LOCATION_ID')  || define('SECOMEXT_LOCATION_ID', 1);

// ===== Entorno =====
defined('APP_ENV')  || define('APP_ENV',            'testing'); //testing o production
defined('TEST_DATOS_RESULT')  || define('TEST_DATOS_RESULT',  'success'); //success o failed
defined('TEST_FOTOS_RESULT')  || define('TEST_FOTOS_RESULT',  'success');

// ===== SmogsBackups (debe ir al final porque usa return) =====
return [
    'smogs_backups' => [
        'base_url' => 'https://smogsbackups.com/restService',
        'timeout'  => 25,
        'usuario'  => 'Mechanical1',
        'password' => 'G7#xk9!Lmp2$VrN3',
        'mode'     => 'testing', //testing o production
        'endpoints' => [
            'testing'    => 'https://postman-echo.com',
            'production' => 'https://smogsbackups.com/restService',
        ],
    ],
];
