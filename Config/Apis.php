<?php
// Config/Apis.php

return [
    'smogs_backups' => [
        'base_url' => 'https://smogsbackups.com/restService',
        'timeout'  => 25,

        // credenciales (las que te dieron)
        'usuario'  => 'Mechanical1',
        'password' => 'G7#xk9!Lmp2$VrN3',

        // switch para pruebas seguras
        'mode' => 'testing', // 'testing' | 'production'

        // endpoints por modo
        'endpoints' => [
            'testing'    => 'https://postman-echo.com', // no registra nada, solo hace eco
            'production' => 'https://smogsbackups.com/restService',
        ],
    ],
];
