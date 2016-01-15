<?php

use ZCMS\Core\ZRouter;

/**
 * Class Router Helper for Index Module
 */
class RouterIndexHelper extends ZRouter
{
    /**
     * @var int
     */
    public $limit = 10;

    /**
     * @var array
     */
    public $routerType = [
        [
            'type' => 'link',
            'menu_name' => 'Home Page',
            'menu_link' => '/'
        ],
        [
            'type' => 'link',
            'menu_name' => 'User Login',
            'menu_link' => '/user/login/'
        ],
        [
            'type' => 'link',
            'menu_name' => 'User Logout',
            'menu_link' => '/user/logout/'
        ],
    ];
}