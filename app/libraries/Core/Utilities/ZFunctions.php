<?php

use Phalcon\Di;
use ZCMS\Core\ZSEO;
use ZCMS\Core\ZSidebar;
use ZCMS\Core\ZTranslate;
use ZCMS\Core\Utilities\URLify;

/**
 * Get sidebar html, use in volt template
 *
 * @param string $sidebarName
 * @return string
 */
function get_sidebar($sidebarName)
{
    return ZSidebar::getSidebar($sidebarName);
}

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 0) return $min;
    $log = log($range, 2);
    $bytes = (int)($log / 8) + 1;
    $bits = (int)$log + 1;
    $filter = (int)(1 << $bits) - 1;
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
}

function get_token($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, strlen($codeAlphabet))];
    }
    return $token;
}

/**
 * Load frontend router
 *
 * @param \Phalcon\Mvc\Router $router
 * @return \Phalcon\Mvc\Router
 */
function zcms_load_frontend_router($router)
{
    //Get frontend module
    $frontendModule = get_child_folder(APP_DIR . '/frontend/');
    $frontendModule = array_reverse($frontendModule);
//    $tmp = [];
    foreach ($frontendModule as $module) {
        $moduleRouterClassName = str_replace(' ', '', ucwords(str_replace('-', ' ', $module)));
        $routerClass = 'Router' . $moduleRouterClassName;
        $fileRoute = APP_DIR . "/frontend/{$module}/{$routerClass}.php";
        if (file_exists($fileRoute)) {
//            $tmp[] = $fileRoute;
            require_once($fileRoute);
            if (class_exists($routerClass)) {
                $router->mount(new $routerClass());
            }
        }
    }
    //echo '<pre>'; var_dump($tmp);echo '</pre>'; die();
    return $router;
}

/**
 * Get translate with code and params
 *
 * @param string $code
 * @param mixed $arrayParams
 * @return string
 */
function __($code, $arrayParams = null)
{
    if ($arrayParams != null && !is_array($arrayParams)) {
        $arrayParams = [$arrayParams];
    }
    $translate = ZTranslate::getInstance()->getTranslate();
    return $translate->_($code, $arrayParams);
}

/**
 * Get SEO for <head> tag
 *
 * @return string
 */
function zcms_header()
{
    return ZSEO::getInstance()->__toString();
}

/**
 * Get SEO prefix for <head> tag
 *
 * @return string
 */
function zcms_header_prefix()
{
    return ZSEO::getInstance()->getHeaderPrefix();
}

/**
 * Remove multi space
 *
 * @param string $str
 * @return mixed
 */
function remove_multi_space($str)
{
    while (strpos($str, '  ') !== false) {
        $str = str_replace('  ', ' ', $str);
    }
    return $str;
    //return preg_replace('/\s\s+/', ' ', $str);
}

/**
 * Load all widget file
 *
 * @param string $location
 */
function zcms_load_widget_file($location = 'frontend')
{
    $allWidget = get_child_folder(APP_DIR . "/widgets/{$location}/");

    foreach ($allWidget as $w) {
        $widgetPath = APP_DIR . "/widgets/{$location}/" . $w . '/' . $w . '.php';
        if (file_exists($widgetPath)) {
            require_once($widgetPath);
        } elseif (DEBUG) {
            /**
             * @var \Phalcon\Flash\Session $flashSession
             */
            $flashSession = Di::getDefault()->get('flashSession');
            $flashSession->error(__('gb_widget_not_found_in_location', ['1' => $w, '2' => $location]));
        }
    }
}

/**
 * Delete dir not empty
 *
 * @param string $dir
 * @return bool
 */
function delete_directory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    //Remove child item
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!$this->_deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }
    return rmdir($dir);
}

/**
 * Get child folder in folder
 *
 * @param string $path
 * @return array
 */
function get_child_folder($path)
{
    $directories = scandir($path);
    $childFolders = [];
    foreach ($directories as $directory) {
        if (strpos($directory, '.') === false && is_dir($path . '/' . $directory)) {
            $childFolders[] = $directory;
        }
    }
    return $childFolders;
}

/**
 * Get files in folder
 *
 * @param string $path
 * @param string $extension
 * @param bool $getFileName
 * @return array
 */
function get_files_in_folder($path, $extension = '', $getFileName = false)
{
    $childFiles = [];
    if (is_dir($path)) {
        $files = scandir($path);
        if (is_array($files)) {
            if ($extension != '') {
                foreach ($files as $file) {
                    $pathFile = $path . '/' . $file;
                    if (strpos($file, '.') !== false && !is_dir($pathFile) && pathinfo($path . '/' . $file, PATHINFO_EXTENSION) == $extension) {
                        if ($getFileName) {
                            $childFiles[] = pathinfo($path . '/' . $file, PATHINFO_FILENAME);
                        } else {
                            $childFiles[] = $file;
                        }
                    }
                }
            } else {
                foreach ($files as $file) {
                    if (strpos($file, '.') !== false && !is_dir($path . '/' . $file)) {
                        $childFiles[] = $file;
                    }
                }
            }
        }
    }
    return $childFiles;
}

/**
 * Get widget information in comment header
 *
 * @param string $widgetFile
 * @return mixed
 */
function get_widget_data($widgetFile)
{
    $defaultDesc = [
        'name' => 'Name',
        'description' => 'Description',
        'version' => 'Version',
        'author' => 'Author',
        'uri' => 'Uri',
        'authorUri' => 'AuthorUri',
        'location' => 'Location'
    ];

    $widgetData = json_decode(file_get_contents(dirname($widgetFile) . DS . 'widget.json'), true);
    foreach ($defaultDesc as $key => $value) {
        $defaultDesc[$key] = $widgetData[$key];
    }

    return $defaultDesc;
}

/**
 * Check and Get router source
 *
 * @param string $path Path file
 * @return bool|array
 */
function check_router($path)
{
    if (!file_exists($path)) {
        return false;
    }

    require $path;

    if (isset($router) && is_array($router)) {
        foreach ($router as $r) {
            if (!isset($r['pattern']) || $r['pattern'] == '') {
                return false;
            }

            if (!isset($r['router']) || !is_array($r['router'])) {
                return false;
            }
        }
        return $router;
    }

    return false;
}

/**
 * Check and get menu module backend
 *
 * @param string $path File path
 * @return false|array
 */
function check_menu($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $dirs = explode('/', dirname($path));
    $moduleBaseName = array_pop($dirs);

    //Require menu file
    require $path;
    if (isset($menu)) {
        if (!isset($menu['rule'])) {
            $menu['rule'] = $moduleBaseName;
        } else {
            $menu['rule'] = strtolower($menu['rule']);
        }

        $cVertical = count(explode('|', $menu['rule']));

        if ($cVertical == 0) {
            return false;
        }

        if (!isset($menu['menu_name'])) {
            $menu['menu_name'] = 'm_admin_' . str_replace('|', '_', $menu['rule']);
        }

        if (!isset($menu['link'])) {
            $menu['link'] = '/admin/' . str_replace('|', '/', $menu['rule']) . '/';
        }

        if (!isset($menu['icon_class'])) {
            $menu['icon_class'] = '';
        }
        if (!isset($menu['link_class'])) {
            $menu['link_class'] = '';
        }
        if (!isset($menu['link_target'])) {
            $menu['link_target'] = '';
        }
        if (isset($menu['items'])) {
            foreach ($menu['items'] as $index => $menuItem) {
                if (!isset($menuItem['rule'])) {
                    return false;
                } else {
                    $menu['items'][$index]['rule'] = strtolower($menuItem['rule']);
                }

                $cVertical = count(explode('|', $menu['items'][$index]['rule']));

                if ($cVertical == 0) {
                    return false;
                }

                if (!isset($menuItem['menu_name'])) {
                    $menu['items'][$index]['menu_name'] = 'm_admin_' . str_replace('|', '_', $menu['items'][$index]['rule']);
                }

                if (!isset($menuItem['link'])) {
                    $menu['items'][$index]['link'] = '/admin/' . str_replace('|', '/', $menu['items'][$index]['rule']) . '/';
                }

                if (!isset($menuItem['icon_class'])) {
                    $menu['items'][$index]['icon_class'] = '';
                }
                if (!isset($menuItem['link_target'])) {
                    $menu['items'][$index]['link_target'] = '';
                }
                if (!isset($menuItem['link_class'])) {
                    $menu['items'][$index]['link_class'] = '';
                }

                if (isset($menuItem['items'])) {
                    foreach ($menuItem['items'] as $index2 => $childMenuItem) {
                        if (!isset($childMenuItem['menu_name']) || $childMenuItem['menu_name'] == '') {
                            return false;
                        }
                        if (!isset($childMenuItem['link'])) {
                            return false;
                        }
                        if (!isset($childMenuItem['rule'])) {
                            return false;
                        } else {
                            $menu['items'][$index]['items'][$index2]['rule'] = strtolower($childMenuItem['rule']);
                        }
                        if (!isset($childMenuItem['icon_class'])) {
                            $menu['items'][$index]['items'][$index2]['icon_class'] = '';
                        }
                        if (!isset($childMenuItem['link_target'])) {
                            $menu['items'][$index]['items'][$index2]['link_target'] = '';
                        }
                        if (!isset($childMenuItem['link_class'])) {
                            $menu['items'][$index]['items'][$index2]['link_class'] = '';
                        }
                    }
                } else {
                    $menuItem['items'][$index] = [];
                }
            }
        } else {
            $menu['items'] = [];
        }
        $menu['module'] = $moduleBaseName;
        return $menu;
    }
    return false;
}

/**
 * Check and get template information
 *
 * @param string $path
 * @return array|bool
 */
function check_template($path)
{
    if (file_exists($path)) {
        $resource = json_decode(file_get_contents($path), true);
        if (is_array($resource)) {
            if (!isset($resource['name']) || $resource['name'] == '') {
                return false;
            }
            if (!isset($resource['description'])) {
                $resource['description'] = '';
            }
            if (!isset($resource['author'])) {
                $resource['author'] = '';
            }
            if (!isset($resource['authorUri'])) {
                $resource['authorUri'] = '';
            }
            if (!isset($resource['version'])) {
                $resource['version'] = '';
            }
            if (!isset($resource['tag'])) {
                $resource['tag'] = '';
            }
            if (!isset($resource['uri'])) {
                $resource['uri'] = '';
            }
            if (isset($resource['config'])) {
                if (is_array($resource['config']) && count($resource['config'])) {
                    foreach ($resource['config'] as $config) {
                        if (!is_array($config) || !isset($config['type']) || !isset($config['value'])) {
                            return false;
                        }
                    }
                }
            }

            if (isset($resource['sidebars'])) {
                if (is_array($resource['sidebars']) && count($resource['sidebars'])) {
                    foreach ($resource['sidebars'] as $sidebars) {
                        if (!is_array($resource) || !isset($sidebars['name']) || !isset($sidebars['baseName'])) {
                            return false;
                        }
                    }
                }
            }

            return $resource;
        }
    }
    return false;
}

/**
 * Unzip file to folder
 *
 * @param string $filePath
 * @param string $pathTo
 * @return bool
 */
function unzip_file($filePath, $pathTo)
{
    $zip = new ZipArchive();
    $res = $zip->open($filePath);
    if ($res === TRUE) {
        $zip->extractTo($pathTo);
        $zip->close();
        return true;
    } else {
        return false;
    }
}

/**
 * Recurse copy source folder to destination folder
 *
 * @param string $source Source path
 * @param string $destination Destination path
 */
function recurse_copy($source, $destination)
{
    $dir = opendir($source);
    @mkdir($destination);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($source . '/' . $file)) {
                recurse_copy($source . '/' . $file, $destination . '/' . $file);
            } else {
                copy($source . '/' . $file, $destination . '/' . $file);
            }
        }
    }
    closedir($dir);
}

/**
 * Check and get resource module
 *
 * @param string $path
 * @param string $moduleBaseName
 * @param string $location
 * @return array|bool
 */
function check_resource($path, $moduleBaseName, $location)
{
    $baseActions = [
        'delete' => 'gb_delete',
        'new' => 'gb_new',
        'addnew' => 'gb_add_new',
        'edit' => 'gb_edit',
        'publish' => 'gb_published',
        'unpublish' => 'gb_unpublished',
        'download' => 'gb_download',
        'trash' => 'gb_trash',
        'moveup' => 'gb_move_up',
        'movedown' => 'gb_move_down',
        'install' => 'gb_install',
        'deletecache' => 'gb_delete_cache',
        'deleteallcache' => 'gb_delete_all_cache',
        'deactivate' => 'gb_deactivate',
        'active' => 'gb_active',
        'update' => 'gb_update'
    ];

    if ($location == 'backend') {
        $prefix = 'm_admin_';
    } else {
        $prefix = 'm_frontend_';
    }

    if (file_exists($path)) {

        require_once $path;

        if (isset($resource) && is_array($resource)) {
            if (!isset($resource['name'])) {
                $resource['name'] = $prefix . $moduleBaseName;
            }

            if (!isset($resource['class_name'])) {
                $resource['class_name'] = 'ZCMS\\Backend\\' . $moduleBaseName . '\\Module';
            }
            if (!isset($resource['path']) || $resource['path'] == '') {
                $resource['path'] = '/backend/' . $moduleBaseName . '/Module.php';
            }

            $resource['location'] = $location;

            if (!isset($resource['description'])) {
                $resource['description'] = $prefix . $moduleBaseName . '_desc';
            }

            if (!isset($resource['author'])) {
                $resource['author'] = '';
            }

            if (!isset($resource['authorUri'])) {
                $resource['authorUri'] = '';
            }

            if (!isset($resource['version'])) {
                $resource['version'] = '';
            }

            if (!isset($resource['uri'])) {
                $resource['uri'] = '';
            }

            if (!isset($resource['acl'])) {
                if (DEBUG) die(__('gb_message_module_resource_must_acl', ['1' => $path]));
                return false;
            }

            if (isset($resource['acl'])) {
                if (!is_array($resource['acl'])) {
                    if (DEBUG) die(__('gb_message_module_resource_acl_must_be_array', ['1' => $path]));
                    return false;
                }

                if (!count($resource['acl'])) {
                    if (DEBUG) die(__('gb_message_module_resource_acl_must_be_rule', ['1' => $path]));
                    return false;
                }
            }

            $resource['rules'] = [];
            foreach ($resource['acl'] as $r) {
                if (!isset($r['controller'])) {
                    if (DEBUG) die(__('gb_message_module_resource_acl_item_must_controller', ['1' => $path]));
                    return false;
                }

                if (!isset($r['controller_name'])) {
                    //$r['controller_name'] = $prefix . $moduleBaseName . '_' . $r['controller'] . '_desc';
                    $r['controller_name'] = $prefix . $moduleBaseName . '_' . $r['controller'];
                }

                if (!isset($r['rules'])) {
                    if (DEBUG) die(__('gb_message_module_resource_acl_item_must_rules', ['1' => $path]));
                    return false;
                }
                if (!is_array($r['rules']) || !count($r['rules'])) {
                    if (DEBUG) die(__('gb_message_module_resource_acl_item_must_rules_item', ['1' => $r['controller'], '2' => $path]));
                    return false;
                }
                foreach ($r['rules'] as $ruleItem) {
                    if (!is_array($ruleItem)) {
                        if (DEBUG) die(__('gb_message_module_resource_structure_rules_in_controller_on_section_acl_item_error', ['1' => $r['controller'], '2' => $path]));
                        return false;
                    }
                }

                foreach ($r['rules'] as $rule) {
                    $tmp = [];
                    $tmp['controller'] = $r['controller'];
                    $tmp['controller_name'] = $r['controller_name'];

                    if (!isset($rule['action'])) {
                        die(__('gb_message_module_resource_must_action_in_rules_on_controller', ['1' => $r['controller'], '2' => $path]));
                    }

                    $tmp['action'] = strtolower($rule['action']);

                    if (!isset($rule['action_name']) || $rule['action_name'] == '') {

                        if (array_key_exists($tmp['action'], $baseActions)) {
                            $tmp['action_name'] = $baseActions[$tmp['action']];
                        } else {
                            $tmp['action_name'] = $prefix . $moduleBaseName . '_' . $r['controller'] . '_' . $rule['action'];
                        }
                    } else {
                        $tmp['action_name'] = $rule['action_name'];
                    }

                    $tmp['sub_action'] = '';
                    if (isset($rule['action'])) {
                        if (isset($rule['sub_action'])) {
                            $tmp['sub_action'] = $rule['sub_action'];
                        }

                        if (strlen($tmp['sub_action'])) {
                            $sub_action = preg_replace('/[^A-Z,0-9\s]/i', '', $tmp['sub_action']);
                            if ($sub_action != $rule['sub_action']) {
                                if (DEBUG) {
                                    die(__('gb_message_module_resource_please_check_sub_action_in_rules_on_controller', ['1' => $rule['sub_action'], '2' => $r['controller'], '3' => $path]));
                                }
                            }
                        }
                    }
                    $resource['rules'][] = $tmp;
                }
            }
            unset($resource['acl']);
            return $resource;
        }
    }
    return false;
}

/**
 * Function register widget on widget file
 *
 * @param string $widgetClassName
 */
function register_widget($widgetClassName)
{
    global $_widget;
    $_widget[] = $widgetClassName;
}

/**
 * HumanTiming
 *
 * @param string $time
 * @return string format time
 */
function human_timing($time)
{
    $currentTime = time();
    $timestamp = strtotime($time);
    $time = $currentTime - $timestamp;

    $tokens = [
        31536000 => __('gb_year'),
        2592000 => __('gb_month'),
        604800 => __('gb_week'),
        86400 => __('gb_day'),
        3600 => __('gb_hour'),
        60 => __('gb_minute'),
        1 => __('gb_second')
    ];

    foreach ($tokens as $unit => $text) {
        if ($time < $unit)
            continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }

    return __('gb_just_now');
}

/**
 * Format number
 *
 * @param string $number
 * @param string $lang
 * @return string
 */
function moneyFormat($number, $lang = 'vi')
{
    $number .= '000';
    $formatter = new MessageFormatter('vi', "{{$lang}, number}");
    return $formatter->format([$number]);
}

/**
 * Format date
 *
 * @param string $date
 * @param string $lang
 * @return string
 */
function date_time_format($date, $lang = 'en_US')
{
    $formatter = new MessageFormatter($lang, "{0, date}");
    return $formatter->format([strtotime($date)]);
}

/**
 * Generate alias from string
 *
 * @param string $str
 * @param string $space_character
 * @param int $length
 * @return string
 */
function generateAlias($str, $space_character = '-', $length = 255)
{
    $str = URLify::filter($str, $length);
    return preg_replace(
        "/[\/_|+ -]+/",
        $space_character,
        strtolower(
            trim(
                preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', iconv('UTF-8', 'ASCII//TRANSLIT', $str)),
                $space_character
            )
        ));
}

/**
 * Validate date
 *
 * @param string $date
 * @param string $format
 * @return bool
 */
function validate_date($date, $format = 'Y-m-d')
{
    $dateTime = \DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) == $date;
}

/**
 * Change date format
 *
 * @param string $date
 * @param string $formatFrom d-m-Y
 * @param string $formatTo Y-m-d
 * @return string
 */
function change_date_format($date, $formatFrom, $formatTo)
{
    if (!$date || !validate_date($date, $formatFrom)) {
        return '';
    }
    return \DateTime::createFromFormat($formatFrom, $date)->format($formatTo);
}

/**
 * Get memory usage
 *
 * @return string
 */
function memory_usage()
{
    $mem_usage = memory_get_usage(true);
    if ($mem_usage < 1024) {
        return $mem_usage . ' B';
    } elseif ($mem_usage < 1048576) {
        return round($mem_usage / 1024, 2) . ' KB';
    } else {
        return round($mem_usage / 1048576, 2) . ' MB';
    }
}

/**
 * Random string
 *
 * @param int $length
 * @param bool|false $specialCharacters
 * @return string
 */
function randomString($length = 22, $specialCharacters = false)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($specialCharacters) {
        $characters = '~!@#$%^&*()_+' . $characters . '~!@#$%^&*()_+';
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}