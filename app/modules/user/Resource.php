<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'namespace' => 'ZCMS\\Modules\\User',
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