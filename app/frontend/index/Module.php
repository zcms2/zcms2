<?php

namespace ZCMS\Frontend\Index;

use Phalcon\DiInterface;
use Phalcon\Loader;
use ZCMS\Core\ZFrontModule;

/**
 * Class Module
 *
 * @package ZCMS\Frontend\Index
 */
class Module extends ZFrontModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'index';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }

    public function registerAutoloaders(DiInterface $dependencyInjector = null)
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            "ZCMS\Frontend\QuizFight" => ROOT_PATH . "/app/frontend/quizFight/",
            "ZCMS\Frontend\QuizFight\Models" => ROOT_PATH . "/app/frontend/quizFight/models/",
            "ZCMS\Frontend\QuizFight\Helpers" => ROOT_PATH . "/app/frontend/quizFight/helpers/",
        ));

        $loader->register();
        parent::registerAutoloaders();
    }
}
