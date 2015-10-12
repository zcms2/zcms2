<?php

namespace ZCMS\Core;

use Phalcon\Mvc\User\Plugin;

/**
 * Class ZRouter
 *
 * @package ZCMS\Core
 */
class ZRouter extends Plugin
{

    /**
     * @var ZRouter
     */
    public static $instance;

    /**
     * @var string
     */
    public static $notFound = '/404-not-found/';

    /**
     * @var array
     */
    public $routerType = [];

    /**
     * Instance ZRouter
     *
     * @return ZRouter
     */
    public static function getInstance()
    {
        $router = get_called_class();
        if (!is_object(self::$instance)) {
            self::$instance = new $router();
        }
        return self::$instance;
    }

    /**
     * Get menu
     *
     * @param string $type
     * @param int $currentPage
     * @param string $title
     * @return mixed
     */
    public function getMenu($type, $currentPage = 1, $title = null)
    {
        $routerBuilder = $type . 'RouterBuilder';
        $routerBuilderLink = $type . 'Router';
        $page = $this->$routerBuilder($currentPage, $title);
        if ($page && count($page->items)) {
            $page->items = $page->items->toArray();
            foreach ($page->items as $index => $item) {
                $page->items[$index]['link'] = $this->$routerBuilderLink($item['alias'], $item['id']);
            }
        }
        return $page;
    }

    /**
     * Get module menu
     *
     * @param $module
     * @return mixed
     */
    public final function getMenuModule($module)
    {
        $menu['name'] = __($module->name);
        $menu['items'] = [];

        if (count($this->routerType)) {
            foreach ($this->routerType as $index => $item) {
                $menu['items'][$index]['title'] = __($item['menu_name']);
                $menu['items'][$index]['type'] = $item['type'];
                if ($item['type'] == 'default') {
                    $menu['items'][$index]['link'] = '/admin/menu/router/build/' . $module->base_name . '/' . $item['menu_type'] . '/1/';
                } else {
                    $menu['items'][$index]['link'] = $item['menu_link'];
                }
            }
        }
        return $menu;
    }

    /**
     * Not found link
     *
     * @return string
     */
    public function notFound()
    {
        return ZRouter::$notFound;
    }
}