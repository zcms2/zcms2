<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'frontend',
    'class_name' => 'ZCMS\\Frontend\\Index\\Module',
    'path' => '/frontend/index/Module.php',
    'acl' =>
        [
            [
                'controller' => 'index',
                'controller_name' => 'Index',
                'rules' =>
                    [

                        [
                            'action' => 'index',
                            'action_name' => 'Front End',
                            'sub_action' => ''
                        ]
                    ]
            ]
        ]
];