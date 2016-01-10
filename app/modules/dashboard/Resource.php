<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'namespace' => 'ZCMS\\Modules\\Dashboard',
    'acl' => [
        [
            'controller' => 'index',
            'controller_name' => 'm_admin_dashboard',
            'rules' =>
                [
                    [
                        'action_name' => 'm_admin_dashboard',
                        'action' => 'index'
                    ]
                ]
        ]
    ]
];