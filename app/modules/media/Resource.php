<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'namespace' => 'ZCMS\\Modules\\Media',
    'acl' => [
        [
            'controller' => 'manager',
            'rules' => [
                [
                    'action' => 'index',
                    'sub_action' => ''
                ],
                [
                    'action' => 'new',
                    'sub_action' => 'edit, publish, unPublish'
                ]
            ]
        ]
    ]
];