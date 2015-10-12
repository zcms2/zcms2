<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'auto_name' => true,
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
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
        [
            'controller' => 'user',
            'rules' => [
                [
                    'action' => 'delete',
                    'sub_action' => ''
                ],
                [
                    'action' => 'active',
                    'sub_action' => ''
                ],
                [
                    'action' => 'deactivate',
                    'sub_action' => ''
                ],
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'edit',
                    'sub_action' => ''
                ],
                [
                    'action' => 'new',
                    'sub_action' => ''
                ]
            ]
        ],
        [
            'controller' => 'role',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'edit',
                    'sub_action' => ''
                ],
                [
                    'action' => 'new',
                    'sub_action' => ''
                ],
                [
                    'action' => 'delete',
                    'sub_action' => ''
                ]
            ]
        ],
        [
            'controller' => 'language',
            'rules' => [
                [
                    'action' => 'publish',
                    'sub_action' => ''
                ],
                [
                    'action' => 'unpublish',
                    'sub_action' => ''
                ],

                [
                    'action' => 'setDefaultLanguage',
                    'sub_action' => ''
                ]
            ]
        ],
        [
            'controller' => 'database',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'backup',
                    'sub_action' => ''
                ]
            ]
        ]
    ]
];