<?php

namespace ZCMS\Backend\Template\Controllers;

use Phalcon\Di as PDI;
use Phalcon\Exception;
use Phalcon\Http\Response as PResponse;
use ZCMS\Core\Models\CoreSidebars;
use ZCMS\Core\Models\CoreTemplates;
use ZCMS\Core\Models\CoreWidgets;
use ZCMS\Core\Models\CoreWidgetValues;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZTranslate;
use ZCMS\Core\ZWidget;

//Load widget file
zcms_load_widget_file();

/**
 * ZCMS SidebarController
 *
 * @package   ZCMS\Core
 * @author    ZCMS Team
 * @copyright ZCMS Team
 * @since     0.0.1
 */
class SidebarController extends ZAdminController
{

    /**
     * Index action
     * List view all widget available and sidebar register in default frontend template
     *
     * @return void
     */
    public function indexAction()
    {
        $displayIcon = '';

        //Add widget frontend translation
        $allWidget = get_child_folder(APP_DIR . '/widgets/frontend/');
        ZTranslate::getInstance()->addWidgetLang($allWidget, 'frontend');

        //Get default frontend template
        $defaultTemplate = CoreTemplates::findFirst("published = 1 AND location = \"frontend\"");

        if ($defaultTemplate && isset($defaultTemplate->base_name)) {
            $defaultTemplate = $defaultTemplate->base_name;
        } else {
            $defaultTemplate = PDI::getDefault()->get("config")->frontendTemplate->defaultTemplate;
        }

        ZTranslate::getInstance()->addTemplateLang($defaultTemplate, 'frontend');

        //Update sidebar with default frontend template
        $this->updateSidebarTemplate($defaultTemplate);

        $widget_html = '';

        //Get widget published
        $_widget = CoreWidgets::find([
            'conditions' => 'published = 1',
        ]);

        foreach ($_widget as $w) {
            $class_name = $w->base_name . "_widget";
            if (class_exists($class_name)) {
                /**
                 * @var ZWidget $current_widget
                 */
                $current_widget = new $class_name();
                $widget_html .= $current_widget->getWidgetHtmlBackend();
            }
        }

        //Set view widget_html
        $this->view->setVar('widget_html', $widget_html);

        /**
         * Find all sidebar register in default frontend template
         *
         * @var CoreSidebars[] $sidebars
         */
        $sidebars = CoreSidebars::find("theme_name = \"" . $defaultTemplate . "\"");

        //Create sidebar html
        $sidebar_html = '';

        $sortable = [];

        $panelAfter = '</div>
                    </div>
                </div>
            </div>';
        $totalSidebar = count($sidebars);
        $half = round($totalSidebar / 2);
        foreach ($sidebars as $index => $sidebar) {
            if ($index == 0) {
                $sidebar_html .= '<div class="col-sm-6 col-sidebar-first no-padding-lr" style="padding-right: 8px">';
            }
            if ($index == 0) {
                $class = 'box-sidebar box-sidebar-first';
            } else {
                $class = 'box-sidebar';
            }
            $panelBefore = '<div class="box ' . $class . '"">
                        <div class="box box-default">
                            <div class="box-header with-border">' . $displayIcon
                . '<h3 class="box-title">' . __($sidebar->sidebar_name) . '</h3>' .
                '<div class="box-tools pull-right"> <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> </div>
                    </div>
                    <div class="box-body">
                        <div class="box">';

            $sidebar_html .= $panelBefore;

            $sortable[] = '#' . $sidebar->sidebar_base_name . '_sortable';

            $sidebar_html .= "<div class=\"zsidebar zsidebar_" . $sidebar->sidebar_base_name . " \" data-content=\"" . $sidebar->sidebar_base_name . "\">
                                <div id=\"" . $sidebar->sidebar_base_name . "_sortable\" class=\"zsidebar-content\">";

            /**
             * @var CoreWidgetValues[] $widget_available_in_sidebar
             */
            $widget_available_in_sidebar = CoreWidgetValues::find(
                [
                    'conditions' => 'theme_name = ?1 AND sidebar_base_name = ?2 AND published = 1',
                    'bind' => [1 => $defaultTemplate, 2 => $sidebar->sidebar_base_name],
                    'order' => 'ordering ASC'
                ]);
            foreach ($widget_available_in_sidebar as $widget_available) {
                if (class_exists($widget_available->class_name)) {
                    $widget_object = new $widget_available->class_name($widget_available->widget_value_id);
                    if (method_exists($widget_object, "getForm")) {
                        $sidebar_html .= $widget_object->getForm();
                    }
                }
            }
            $sidebar_html .= '</div></div>' . $panelAfter;

            if ($index == $half - 1) {
                $sidebar_html .= '</div><div class="col-sm-6 col-sidebar-last no-padding-lr" style="padding-left: 7px">';
            }

            if ($index == $totalSidebar - 1) {
                $sidebar_html .= '</div>';
            }

        }

        //Set view sidebar_html
        $this->view->setVar("sidebar_html", $sidebar_html);
        $this->view->setVar('sortable', implode(', ', $sortable));
        $this->assets->collection("css_header")->addJs('/templates/backend/' . $this->_defaultTemplate . "/zcms/css/sidebar-widget.css");
    }

    /**
     *
     * Update sidebar template
     *
     * @param string $defaultTemplate
     */
    public function updateSidebarTemplate($defaultTemplate)
    {

        //global $_sidebar;

        $pathTemplate = APP_DIR . '/templates/frontend/' . $defaultTemplate . '/template.json';
        if ($resource = check_template($pathTemplate)) {
            $_sidebars = $resource['sidebars'];
            if (count($_sidebars)) {
                /**
                 * @var CoreSidebars[] $allSidebar
                 */
                $allSidebar = CoreSidebars::find([
                    'conditions' => 'theme_name = ?1',
                    'bind' => [1 => $defaultTemplate]
                ]);

                $sidebarBaseNames = array_column($_sidebars, 'baseName');

                //Remove old sidebar in current templates
                foreach ($allSidebar as $oldSidebar) {
                    if (!in_array($oldSidebar->sidebar_base_name, $sidebarBaseNames)) {
                        /**
                         * @var CoreWidgetValues[] $oldWidgetsValueInSidebar
                         */
                        $oldWidgetsValueInSidebar = CoreWidgetValues::find([
                            'conditions' => 'theme_name = ?0 AND sidebar_base_name = ?1',
                            'bind' => [$defaultTemplate, $oldSidebar->sidebar_base_name]
                        ]);
                        foreach ($oldWidgetsValueInSidebar as $oldWidgetValueInSidebar) {
                            $oldWidgetValueInSidebar->delete();
                        }
                        $oldSidebar->delete();
                    }
                }

                foreach ($_sidebars as $s) {
                    $CoreSidebars = CoreSidebars::findFirst([
                        'conditions' => 'sidebar_base_name = ?1 AND theme_name = ?2',
                        'bind' => [1 => $s['baseName'], 2 => $defaultTemplate]
                    ]);

                    if (is_object($CoreSidebars) && isset($CoreSidebars->sidebar_base_name)) {
                        $CoreSidebars->sidebar_base_name = $s['baseName'];
                        $CoreSidebars->save();
                    } else {
                        $CoreSidebars = new CoreSidebars();
                        $CoreSidebars->sidebar_base_name = $s['baseName'];
                        $CoreSidebars->theme_name = $defaultTemplate;
                        $CoreSidebars->sidebar_name = $s['name'];
                        $CoreSidebars->ordering = 0;
                        $CoreSidebars->published = 1;
                        $CoreSidebars->location = 'frontend';
                        $CoreSidebars->save();
                    }
                }

            }
        }
    }

    /**
     * Add new widget
     *
     * @return string Ajax
     */
    public function addNewWidgetAction()
    {
        $response = new PResponse();
        $content = '';
        $response->setHeader("Content-Type", "application/json");

        if ($this->request->isAjax()) {
            //Add widget frontend translation
            $allWidget = get_child_folder(APP_DIR . '/widgets/frontend/');
            ZTranslate::getInstance()->addWidgetLang($allWidget, 'frontend');

            /**
             * @var CoreTemplates $defaultFrontEndTemplate
             */
            $defaultFrontEndTemplate = CoreTemplates::findFirst("location = 'frontend' AND published = 1");
            $theme_name = $defaultFrontEndTemplate->base_name;
            $widget_class = $this->request->getPost('widget_class', 'string', '');
            $index = $this->request->getPost('index', 'int', 1);
            $sidebar_name = $this->request->getPost('sidebar_name', 'string', '');

            $CoreSidebars = CoreSidebars::findFirst([
                'conditions' => 'sidebar_base_name = ?1 AND theme_name = ?2 AND location = ?3',
                'bind' => [1 => $sidebar_name, 2 => $theme_name, 3 => 'frontend']
            ]);

            if (!$CoreSidebars) {
                $CoreSidebars = new CoreSidebars();
                $CoreSidebars->sidebar_base_name = $sidebar_name;
                $CoreSidebars->theme_name = $theme_name;
                $CoreSidebars->location = 'frontend';
                $CoreSidebars->save();
            }

            if (is_object($CoreSidebars) && $CoreSidebars) {
                try {
                    /**
                     * @var ZWidget $widget
                     */
                    $widget = new $widget_class();
                    $widget->save($sidebar_name, $index, null, $theme_name);
                    $content = $widget->getForm(true);
                } catch (Exception $e) {

                }
            }
        }
        $response->setJsonContent($content);
        return $response;
    }

    /**
     * Save widget action
     *
     * @return string Ajax
     */
    public function addSaveWidgetAction()
    {
        $response = new PResponse();
        $content = 0;
        $response->setHeader("Content-Type", "application/json");

        if ($this->request->isAjax() || 1) {
            $widgetId = $this->request->get('zwidget_id', 'int', 0);
            /**
             * @var CoreWidgetValues $widgetValue
             */
            $widgetValue = CoreWidgetValues::findFirst($widgetId);
            if ($widgetValue) {
                $data = $this->request->get(strtolower($widgetValue->class_name));
                if (isset($data["{$widgetValue->widget_value_id}"])) {
                    $widgetValue->options = json_encode($data["{$widgetValue->widget_value_id}"]);
                    if ($widgetValue->save()) {
                        $content = 1;
                    }
                }
            }
        }
        $response->setJsonContent($content);
        return $response;
    }

    /**
     * Delete widget action
     *
     * @return string Ajax
     */
    public function deleteWidgetAction()
    {
        $response = new PResponse();
        $content = '';
        $response->setHeader("Content-Type", "application/json");

        if ($this->request->isAjax()) {
            $widgetId = $this->request->getPost('zwidget_id', 'int', 0);
            $widget = CoreWidgetValues::findFirst($widgetId);
            if ($widget) {
                if ($widget->delete()) {
                    $content = '1';
                }
            }
        }

        $response->setJsonContent($content);
        return $response;
    }

    /**
     * Update widget order
     *
     * @return PResponse
     * @throws Exception
     */
    public function updateWidgetOrderAction()
    {
        $response = new PResponse();
        $content = '';
        $response->setHeader("Content-Type", "application/json");

        if ($this->request->isAjax()) {
            $widget_id = $this->request->getPost('widget_id', 'int');
            $newIndex = $this->request->getPost('index', 'int');
            $sidebar = str_replace(' ', '', $this->request->getPost('sidebar_name', ['string', 'striptags']));
            if ($widget_id && $newIndex && $sidebar) {
                /**
                 * @var CoreWidgetValues $widget
                 */
                $widget = CoreWidgetValues::findFirst($widget_id);
                /**
                 * @var CoreSidebars $CoreSidebars
                 */
                $CoreSidebars = CoreSidebars::findFirst(['conditions' => 'sidebar_base_name = ?1', 'bind' => [1 => $sidebar]]);

                if ($widget && $CoreSidebars) {
                    /**
                     * @var CoreTemplates $defaultFrontendTemplate
                     */
                    $defaultFrontendTemplate = CoreTemplates::findFirst("location = 'frontend' AND published = 1");
                    $themeName = $defaultFrontendTemplate->base_name;
                    $widget->reOder('sidebar_base_name = ?1', [1 => $CoreSidebars->sidebar_base_name]);
                    $widget->reOder('sidebar_base_name = ?1', [1 => $widget->sidebar_base_name]);

                    if ($widget->ordering > $newIndex) {
                        $queryUp = "UPDATE core_widget_values SET ordering = ordering + 1 WHERE ordering >= {$newIndex} AND theme_name = '{$themeName}' AND sidebar_base_name = '{$sidebar}'";
                        $queryDown = "UPDATE core_widget_values SET ordering = ordering - 1 WHERE ordering < {$newIndex} AND theme_name = '{$themeName}' AND sidebar_base_name = '{$sidebar}'";
                    } elseif ($widget->ordering < $newIndex) {
                        $queryUp = "UPDATE core_widget_values SET ordering = ordering + 1 WHERE ordering > {$newIndex} AND theme_name = '{$themeName}' AND sidebar_base_name = '{$sidebar}'";
                        $queryDown = "UPDATE core_widget_values SET ordering = ordering - 1 WHERE ordering <= {$newIndex} AND theme_name = '{$themeName}' AND sidebar_base_name = '{$sidebar}'";
                    }

                    if (isset($queryUp) && isset($queryDown)) {
                        $this->db->execute($queryUp);
                        $this->db->execute($queryDown);
                    }

                    $widget->ordering = $newIndex;
                    $widget->sidebar_base_name = $CoreSidebars->sidebar_base_name;
                    if ($widget->save()) {
                        $content = '1';
                        $widget->reOder('sidebar_base_name = ?1', [1 => $sidebar]);
                        $widget->reOder('sidebar_base_name = ?1', [1 => $CoreSidebars->sidebar_base_name]);
                    } else {
                        //Do something
                    }
                }
            }
        }
        $response->setJsonContent($content);
        return $response;
    }
}
