<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'namespace' => 'ZCMS\\Modules\\Template',
    'acl' => [
        [
            'controller' => 'index',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => 'active,unActive'
                ],
                [
                    'action' => 'install',
                    'sub_action' => ''
                ]
            ]
        ],
        [
            'controller' => 'sidebar',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ]
            ]
        ],
        [
            'controller' => 'widget',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => 'publish, unpublish'
                ]
            ]
        ]
    ]
];