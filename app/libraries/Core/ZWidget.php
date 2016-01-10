<?php

namespace ZCMS\Core;

use Phalcon\Di;
use Phalcon\Tag;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use ZCMS\Core\Models\CoreWidgets;
use ZCMS\Core\Models\CoreTemplates;
use ZCMS\Core\Models\CoreWidgetValues;
use ZCMS\Core\Models\Users;

/**
 * CLass Widget [Frontend]
 *
 * @package   ZCMS\Core
 *
 * @since     0.0.1
 */
class ZWidget
{
    /**
     * Widget id
     *
     * @var integer
     */
    public $_id;

    /**
     * Base name [a-z0-9]
     * @var string
     */
    public $widget_class_name;

    /**
     * Widget Name (Folder Widget) [a-z0-9]
     * @var string
     */
    public $_widget_name;

    /**
     * @var string
     */
    public $_title;

    /**
     * Description
     *
     * @var string
     */
    public $_description;

    /**
     * New option when save
     *
     * @var array
     */
    public $newOptions;

    /**
     * Widget option
     *
     * @var array|mixed
     */
    public $options = [];

    /**
     * Check widget error
     *
     * @var bool
     */
    public $validation = true;

    /**
     * @var array
     */
    public $_layout = [];

    /**
     * @var View
     */
    protected $view;

    /**
     * @var \Phalcon\Db\Adapter\Pdo\Postgresql
     */
    private $db;

    /**
     * @var \stdClass
     */
    private $config;

    /**
     * Instance construct
     *
     * @param integer $id
     * @param array $widgetInfo
     * @param array $options
     */
    public function __construct($id = null, $widgetInfo = [], $options = [])
    {
        $this->db = Di::getDefault()->get('db');
        $this->config = Di::getDefault()->get('config');
        $this->validation = $this->checkOptions($options);
        if ($this->validation) {
            $this->newOptions = $options;
            $id = (int)$id;
            if ($id > 0) {
                /**
                 * @var CoreWidgetValues $widget
                 */
                $widget = CoreWidgetValues::findFirst([
                    'conditions' => 'widget_value_id = ?0',
                    'bind' => [$id]
                ]);
                if (!$widget) {
                    $this->options = new \stdClass();
                    $this->options->_layout = 'default';
                } else {
                    $this->options = json_decode($widget->options);
                    $this->_id = $id;
                }
            }
            $this->_widget_name = explode('_', get_class($this))[0];
            $this->widget_class_name = strtolower(get_class($this));

            /**
             * @var CoreWidgets $widgetInfoDb
             */
            $widgetInfoDb = CoreWidgets::findFirst([
                'conditions' => 'lower(base_name) = ?0',
                'bind' => [str_replace('_widget', '', $this->widget_class_name)]
            ]);

            $widgetInfo['_layout'] = 'default';

            if (isset($widgetInfo['title'])) {
                $this->_title = __($widgetInfo['title']);
            } else {
                $this->_title = __($widgetInfoDb->title);
            }

            if (isset($widgetInfo['description'])) {
                $this->_description = __($widgetInfo['description']);
            } else {
                $this->_description = __($widgetInfoDb->description);
            }
            $this->_layout = $this->_getLayout($this->_widget_name);
        }
    }


    /**
     * Get layout for this widget
     *
     * @param $widgetName
     * @return string Select widget templates
     */
    private function _getLayout($widgetName)
    {
        $files = get_files_in_folder(ROOT_PATH . '/app/widgets/frontend/' . $widgetName . '/views/', 'volt', true);
        array_unshift($files, 'default');
        $overrideWidgetFolder = ROOT_PATH . '/app/templates/frontend/' . $this->config->frontendTemplate->defaultTemplate . '/widgets/' . $widgetName . DS;
        if (is_dir($overrideWidgetFolder)) {
            $filesOverride = get_files_in_folder($overrideWidgetFolder, 'volt', true);
            foreach ($filesOverride as $file) {
                if (!in_array($file, $files)) {
                    $files[] = $file;
                }
            }

        }

        $selectItems = [];
        foreach ($files as $index => $file) {
            $selectItems[$file] = ucwords(str_replace(['_', '-'], ' ', $file));
        }

        //echo '<pre>'; var_dump($selectItems);echo '</pre>'; die();

        return '<p><label for="' . $this->getFieldId('_layout') . '">' . __('gb_layout') . '</label>'
        . Tag::select([$this->getFieldName('_layout'), $selectItems,
            'class' => 'form-control input-sm',
            'value' => isset($this->options->_layout) ? $this->options->_layout : ""
        ])
        . '</p>';
    }

    /**
     * Init Widget
     */
    public function initialize()
    {

    }

    /**
     * Get form. Coder must code overwrite in child class
     *
     * @return string
     */
    public function form()
    {
        return "<p class=\"no-options-widget\">" . __("There are no options for this widget.") . "</p>";
    }

    /**
     * Render form in sidebar
     *
     * @param bool $openClass
     * @return string
     */
    public final function getForm($openClass = false)
    {
        if ($this->validation) {
            if (isset($this->options->title) && $this->options->title) {
                $title = $this->_title . ": " . $this->options->title;
            } else {
                $title = $this->_title;
            }
            if ($openClass) {
                $class = 'widget-open';
                $style = 'display: block;';
            } else {
                $class = 'widget-close';
                $style = 'display: none;';
            }

            $result = '<div class="widget_active" data-content="' . $this->widget_class_name . '" data-content-id="' . $this->_id . '" id="p-widget-id-' . $this->_id . '">
                            <div class="zwidget-title ' . $class . '">' . __($title) . '<span class="caret"></span></div>
                            <div class="zForm" style="' . $style . '">
                                <form action="/" method="post" id="widget-id-' . $this->_id . '">
                                    ' . $this->form() . $this->_layout . '
                                    <input type="hidden" name="zwidget_id" value="' . $this->_id . '">
                                    <div class="widget-control">
                                        <a href="#" data-loading-text="Saving..." data-complete-text="Save" class="btn btn-sm btn-success widget-control-save right">Save</a>
                                        <a href="#" class="btn btn-sm btn-warning widget-control-delete">Delete</a>
                                    </div>
                                </form>
                            </div>
                        </div>';
            return $result;
        } else {
            return "Widget must be name!";
        }
    }

    /**
     * Coder must code overwrite in child class
     *
     * @return string
     */
    public function widget()
    {
        return $this->options;
    }

    /**
     * Widget
     *
     * @return string
     */
    public final function getWidget()
    {
        $this->__initView();
        $this->widget();
        $this->view->start();
        if (!isset($this->options->_layout) || empty($this->options->_layout)) {
            $this->options->_layout = 'default';
        }
        $overrideFolder = ROOT_PATH . '/app/templates/frontend/' . $this->config->frontendTemplate->defaultTemplate . '/widgets/';
        $overrideFile = $overrideFolder . $this->_widget_name . DS . $this->options->_layout . '.volt';
        if (file_exists($overrideFile)) {
            $this->view->setViewsDir($overrideFolder);
            $this->view->render($this->_widget_name, $this->options->_layout)->getContent();
        } else {
            $this->view->setViewsDir(ROOT_PATH . '/app/widgets/frontend/' . $this->_widget_name . DS);
            $this->view->render('views', $this->options->_layout)->getContent();
        }
        $this->view->finish();
        $content = $this->view->getContent();
        $html = $this->beforeWidget();
        if (isset($this->options->title) && $this->options->title != '') {
            $html .= $this->beforeTitle() . $this->options->title . $this->afterTitle();
        }
        $html .= '<div class="widget-content">' . $content . '</div></div>';
        return $html;
    }

    private function __initView()
    {
        $this->view = new View();
        $this->view->setVar('_baseUri', BASE_URI);
        $this->view->setVar('_user',Users::getCurrentUser());
        $this->view->setDI(Di::getDefault());
        $this->view->registerEngines([
            '.volt' => function ($view, $di) {
                $volt = new Volt($view, $di);

                $volt->setOptions([
                    'compiledPath' => function ($templatePath) {
                        $templatePath = strstr($templatePath, '/app');
                        $dirName = dirname($templatePath);

                        if (!is_dir(ROOT_PATH . '/cache/volt' . $dirName)) {
                            mkdir(ROOT_PATH . '/cache/volt' . $dirName, 0755, TRUE);
                        }
                        return ROOT_PATH . '/cache/volt' . $dirName . '/' . basename($templatePath, '.volt') . '.php';
                    },
                    'compileAlways' => method_exists($di, 'get') ? (bool)($di->get('config')->backendTemplate->compileTemplate) : false
                ]);
                $compiler = $volt->getCompiler();
                $compiler->addFunction('get_sidebar', 'get_sidebar');
                $compiler->addFunction('__', '__');
                $compiler->addFunction('strtotime', 'strtotime');
                $compiler->addFunction('human_timing', 'human_timing');
                $compiler->addFunction('moneyFormat', 'moneyFormat');
                $compiler->addFunction('number_format', 'number_format');
                $compiler->addFunction('change_date_format', 'change_date_format');
                $compiler->addFunction('in_array', 'in_array');
                return $volt;
            }
        ]);
    }

    /**
     * Update
     *
     * @param $newInstance
     * @return mixed
     */
    public function update($newInstance)
    {
        return $newInstance;
    }

    /**
     * Get html drag drop widget in left view Sidebar manager
     *
     * @return string
     */
    public final function getWidgetHtmlBackend()
    {
        $result = "<div class=\"widget_active widget_new widget\" data-content=\"" . strtolower(get_class($this)) . "\">
                        <div class=\"widget_active_panel\">
                            <div class=\"zwidget-title\">" . __($this->_title) . "</div>
                            <div class=\"zwidget-description\">" . __($this->_description) . "</div>
                        </div>
                   </div>";
        return $result;
    }

    /**
     * Check options
     *
     * @param array|mixed $options
     * @return bool
     */
    public final function checkOptions($options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $item) {
                if (is_array($item) || is_object($item)) {
                    return false;
                } else {
                    $key = (string)$key;
                    if (str_replace(" ", "", $key) != $key) {
                        return false;
                    }
                }
            }
            return true;
        }
        return true;
    }

    /**
     * Save widget
     *
     * @param $sidebar
     * @param integer $ordering
     * @param integer $id
     * @param mixed $newInstance
     * @param string $themeName
     * @return bool
     */
    public final function save($sidebar, $ordering = null, $id = null, $newInstance = null, $themeName = null)
    {
        $ordering = intval($ordering);
        if ($this->validation) {
            if (!$themeName) {
                /**
                 * @var CoreTemplates $CoreTemplates
                 */
                $CoreTemplates = CoreTemplates::findFirst("location = 'frontend' AND published = 1");
                $themeName = $CoreTemplates->base_name;
            }
            /**
             * @var CoreWidgetValues $CoreWidgetValues
             */
            if ((int)$id) {
                $CoreWidgetValues = CoreWidgetValues::findFirst($id);
            } else {
                $CoreWidgetValues = new CoreWidgetValues();
            }

            $CoreWidgetValues->reOder('sidebar_base_name = ?0', [0 => $sidebar]);
            $queryUp = "UPDATE core_widget_values SET ordering = ordering + 1 WHERE ordering >= {$ordering} AND theme_name = '{$themeName}' AND sidebar_base_name = '{$sidebar}'";
            $queryDown = "UPDATE core_widget_values SET ordering = ordering - 1 WHERE ordering < {$ordering} AND theme_name = '{$themeName}' AND sidebar_base_name = '{$sidebar}'";

            $this->db->execute($queryDown);
            $this->db->execute($queryUp);

            $CoreWidgetValues->sidebar_base_name = $sidebar;
            $CoreWidgetValues->theme_name = $themeName;
            $CoreWidgetValues->class_name = $this->_widget_name . '_Widget';
            $CoreWidgetValues->options = $this->_processOptions($id, $newInstance);
            $CoreWidgetValues->published = 1;
            $CoreWidgetValues->ordering = $ordering;
            $CoreWidgetValues->title = $this->_title;
            if ($CoreWidgetValues->save()) {
                //Do something
            } else {
                //Do something
            }
            $this->_id = $CoreWidgetValues->widget_value_id;
            $CoreWidgetValues->reOder('sidebar_base_name = ?0', [0 => $sidebar]);
            return true;
        }

        return false;
    }

    /**
     * Process options
     *
     * @param integer $id
     * @param mixed $newInstance
     * @return mixed|string
     */
    private function _processOptions($id = null, $newInstance = null)
    {
        if ($id) {
            return json_encode($newInstance);
        }
        return json_encode($this->newOptions);
    }

    /**
     * Get field name
     *
     * @param string $name
     * @return string
     */
    public final function getFieldName($name)
    {
        return $this->widget_class_name . "[" . $this->_id . "][" . $name . "]";
    }

    /**
     * Helper get id unique in widget
     *
     * @param string $name
     * @return string
     */
    public final function getFieldId($name = null)
    {
        if ($name != null) {
            return $this->widget_class_name . "_" . $this->_id . "_" . $name;
        }
        return $this->widget_class_name . "_" . $this->_id;
    }

    /**
     * Html before widget
     *
     * @return string
     */
    public function beforeWidget()
    {
        return '<div class="widget-item" id="widget-item-' . $this->_id . '">';
    }

    /**
     * Html after widget
     * @return string
     */
    public function afterWidget()
    {
        return '</div>';
    }

    /**
     * Html before title
     *
     * @return string
     */
    public function beforeTitle()
    {
        return '<h3 class="title">';
    }

    /**
     * Html after title
     *
     * @return string
     */
    public function afterTitle()
    {
        return '</h3>';
    }
}