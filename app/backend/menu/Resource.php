<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\Menu\\Module',
    'path' => '/backend/menu/Module.php',
    'acl' => [
        [
            'controller' => 'index',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'new',
                    'sub_action' => ''
                ],
                [
                    'action' => 'edit',
                    'sub_action' => ''
                ],
                [
                    'action' => 'delete',
                    'sub_action' => '',
                ]
            ]
        ],
        [
            'controller' => 'menuitem',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'new',
                    'sub_action' => ''
                ],
                [
                    'action' => 'edit',
                    'sub_action' => ''
                ],
                [
                    'action' => 'delete',
                    'sub_action' => ''
                ]
            ]
        ]
    ]
];