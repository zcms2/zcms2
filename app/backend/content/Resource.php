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