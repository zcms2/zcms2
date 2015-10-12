<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\User\\Module',
    'path' => '/backend/user/Module.php',
    'acl' => [
        [
            'controller' => 'profile',
            'rules' => [
                [
                    'action' => 'index'
                ]
            ]
        ]
    ]
];