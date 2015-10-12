<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\Admin\\Module',
    'path' => '/backend/admin/Module.php',
    'acl' => [
        [
            'controller' => 'index',
            'controller_name' => 'm_admin_admin',
            'rules' =>
                [
                    [
                        'action_name' => 'm_admin_admin',
                        'action' => 'index'
                    ]
                ]
        ]
    ]
];