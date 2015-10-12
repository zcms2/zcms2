<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\Slide\\Module',
    'path' => '/backend/slide/Module.php',
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
                    'sub_action' => 'edit, publish, unPublish'
                ],
                [
                    'action' => 'delete',
                    'sub_action' => ''
                ]
            ]
        ],
        [
            'controller' => 'manage-slide',
            'rules' => [
                [
                    'action' => 'slide',
                    'sub_action' => ''
                ],
                [
                    'action' => 'new',
                    'sub_action' => 'edit, publish, unPublish'
                ],
                [
                    'action' => 'delete',
                    'sub_action' => ''
                ]
            ]
        ]
    ]
];