<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\Content\\Module',
    'path' => '/backend/content/Module.php',
    'acl' => [
        [
            'controller' => 'categories',
            'rules' => [
                [
                    'action' => 'view',
                    'sub_action' => 'index'
                ],
                [
                    'action' => 'view_all',
                    'sub_action' => 'index, view'
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
                    'action' => 'publish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'unPublish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'edit_all',
                    'sub_action' => 'edit'
                ],
                [
                    'action' => 'publish_all',
                    'sub_action' => 'publish'
                ],
                [
                    'action' => 'unPublish_all',
                    'sub_action' => 'unPublish'
                ]
            ]
        ],
        [
            'controller' => 'posts',
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
                    'action' => 'publish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'unPublish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'edit_all',
                    'sub_action' => 'edit'
                ],
                [
                    'action' => 'publish_all',
                    'sub_action' => 'publish'
                ],
                [
                    'action' => 'unPublish_all',
                    'sub_action' => 'unPublish'
                ]
            ]
        ]
    ]
];