<?php
$resource = [
    'name' => 'm_frontend_index',
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'namespace' => 'ZCMS\\Modules\\Index',
    'acl' => [
        [
            'controller' => 'module',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'publish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'unpublish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'update',
                    'sub_action' => ''
                ],
                [
                    'action' => 'delete',
                    'sub_action' => ''
                ]
            ]
        ],
    ]
];