<?php

use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\RemoteAddr;
use Laminas\Session\Validator\HttpUserAgent;

return [
    'session_config' => [
        'cookie_lifetime'     => 60 * 60 * 24,
        'gc_maxlifetime'      => 60 * 60 * 24 * 30,
    ],
    'session_manager' => [
        'validators' => []
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    'session_containers' => [
        'DatosSession'
    ],
    'service_manager' => [
        'factories' => [
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
        ]
    ]
];
