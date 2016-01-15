<?php

namespace ZCMS\Core;

use Phalcon\DI;
use ZCMS\Core\Cache\ZCache;
use ZCMS\Core\Models\CoreOptions;
use ZCMS\Core\Models\CorePhpLogs;
use Phalcon\Mvc\Application as PApplication;

/**
 * Class ZApplication
 *
 * @package ZCMS\Application
 * @property
 */
class ZApplication extends PApplication
{
    use ZApplicationInit;

    /**
     * @var mixed
     */
    protected $config;

    /**
     * Instance construct
     */
    public function __construct()
    {
        /**
         * Create default DI
         */
        $this->di = new DI\FactoryDefault();
        $this->config = ZFactory::config();
        if (isset($_SESSION['ZCMS_APPLICATION_LANGUAGE'])) {
            define('ZCMS_APPLICATION_LANGUAGE', $_SESSION['ZCMS_APPLICATION_LANGUAGE']);
        } else {
            define('ZCMS_APPLICATION_LANGUAGE', $this->config->website->language);
        }
        if ($this->config->website->baseUri == '') {
            if ($_SERVER['SERVER_PORT'] != '443') {
                $this->config->website->baseUri = 'http://' . $_SERVER['HTTP_HOST'] . str_replace(['/public/index.php', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
            } else {
                $this->config->website->baseUri = 'https://' . $_SERVER['HTTP_HOST'] . str_replace(['/public/index.php', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
            }

        }

        $this->di->set('config', $this->config);
        /**
         * @define bool DEBUG
         */
        define('DEBUG', $this->config->debug);

        /**
         * @define string BASE_URI
         */
        define('BASE_URI', $this->config->website->baseUri);
        include_once ROOT_PATH . '/app/libraries/Core/Utilities/ZFunctions.php';

        parent::__construct($this->di);
    }

    /**
     * Run application
     *
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function run()
    {
        $this->_initLoader($this->_dependencyInjector);

        $this->_initServices($this->_dependencyInjector, $this->config);

        $this->_initModules();

        $handle = $this->handle();

        if ($this->config->logError) {
            $error = error_get_last();
            if ($error) {
                $logKey = md5(implode('|', $error));
                /**
                 * @var $corePhpLog CorePhpLogs
                 */
                $corePhpLog = CorePhpLogs::findFirst([
                    'conditions' => 'log_key = ?0',
                    'bind' => [$logKey]
                ]);

                if ($corePhpLog) {
                    $corePhpLog->status = 0;
                } else {
                    $corePhpLog = new CorePhpLogs();
                    $corePhpLog->assign([
                        'log_key' => $logKey,
                        'type' => $error['type'],
                        'message' => $error['message'],
                        'file' => $error['file'],
                        'line' => $error['line'],
                        'status' => 0
                    ]);
                }
                $corePhpLog->save();
            }
        }

        return $handle;
    }

    /**
     * Auto load module from database
     */
    public function _initModules()
    {
        //Create new cache
        $cache = ZCache::getCore();

        CoreOptions::initOptions();

        //Load module
        $registerModules = $cache->get(ZCMS_CACHE_MODULES);
        if ($registerModules === null) {
            /**
             * @var \Phalcon\Db\Adapter\Pdo\Postgresql $db
             */
            $db = $this->di->get('db');
            $query = 'SELECT base_name, namespace FROM core_modules WHERE published = 1';
            $modules = $db->fetchAll($query);
            $registerModules = [];
            foreach ($modules as $module) {
                $registerModules[$module['base_name']] = [
                    'baseName' => $module['base_name'],
                    'namespace' => $module['namespace'],
                    'className' => $module['namespace'] . '\\Module',
                    'path' => ROOT_PATH . '/app/modules/Module.php'
                ];
            }
            $cache->save(ZCMS_CACHE_MODULES, $registerModules);
        }
        global $_modules;
        $_modules = $registerModules;
        $this->registerModules($registerModules);
    }
}
