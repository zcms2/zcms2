<?php

namespace ZCMS\Backend\Template\Controllers;

use ZCMS\Core\Models\CoreWidgets;

use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\ZTranslate;

/**
 * Class WidgetController
 *
 * @package ZCMS\Backend\Template\Controllers
 */
class WidgetController extends ZAdminController
{
    /**
     * @var string PHQL Model
     */
    public $_model = 'ZCMS\Core\CoreWidgets';

    /**
     * @var string Model name in database
     */
    public $_modelBaseName = 'core_widgets';

    /**
     *
     * @var string
     */
    public $_modelPrimaryKey = 'widget_id';

    /**
     * List all widgets
     */
    public function indexAction()
    {
        //Update info all widget
        $this->updateInfoAllWidget();

        //Add toolbar button
        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();

        //Add filter
        $this->addFilter('filter_order', 'title', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');

        //Get all filter
        $filter = $this->getFilter();

        //Create conditions
        $conditions = [];

        //Get Items
        $items = CoreWidgets::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

        //Current page
        $currentPage = $this->request->getQuery('page', 'int');
        $paginationLimit = $this->config->pagination->limit;

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationModel($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);

        //Set column name, value
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'widget_id'
            ],
            [
                'type' => 'index',
            ],
            [
                'type' => 'text',
                'title' => 'gb_widget_name',
                'column' => 'title',
                'translation' => true,
                'sort' => false
            ],
            [
                'type' => 'text',
                'title' => 'gb_description',
                'column' => 'description',
                'translation' => true,
                'sort' => false
            ],
            [
                'type' => 'text',
                'title' => 'gb_author',
                'class' => 'text-center',
                'column' => 'author'
            ],
            [
                'type' => 'text',
                'title' => 'gb_version',
                'class' => 'text-center',
                'column' => 'version'
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'column' => 'published',
                'link' => '/admin/template/widget/',
                'access' => $this->acl->isAllowed('template|widget|published')
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'widget_id'
            ]
        ]);
    }

    /**
     * Publish action
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     */
    public function publishAction($id = null, $redirect = null, $log = true)
    {
        parent::publishAction($id, $redirect, false);
    }

    /**
     * UnPublish action
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     */
    public function unPublishAction($id = null, $redirect = null, $log = true)
    {
        parent::unPublishAction($id, $redirect, false);
    }

    /**
     * Publish widget in sidebar
     *
     * @param $widgetBaseName
     */
    protected final function unpublishWidgetInSidebar($widgetBaseName)
    {
        $query = "DELETE FROM core_widget_values WHERE class_name = '" . $widgetBaseName . "_widget.'";
        $this->db->query($query);
    }

    /**
     * Update widget info
     */
    protected final function updateInfoAllWidget()
    {
        $allWidget = get_child_folder(APP_DIR . '/widgets/frontend/');
        ZTranslate::getInstance()->addWidgetLang($allWidget, 'frontend');
        $this->deleteOldWidget($allWidget);

        foreach ($allWidget as $w) {
            $widgetPath = APP_DIR . '/widgets/frontend/' . $w . '/' . $w . '.php';
            $infoWidget = get_widget_data($widgetPath);
            $infoWidget['title'] = $infoWidget['name'];

            $widget = CoreWidgets::findFirst([
                'conditions' => 'base_name = ?0',
                'bind' => [0 => $w]
            ]);

            if (!$widget) {
                $widget = new CoreWidgets();
                $widget->base_name = $w;
                $widget->published = 0;
                $widget->is_core = 0;
            }

            $keys = [
                'title',
                'description',
                'version',
                'author',
                'uri',
                'authorUri',
                'location'
            ];

            foreach ($keys as $key) {
                $widget->$key = $infoWidget[$key];
            }

            if (isset($widget->location) && (strtolower($widget->location) == 'frontend' || strtolower($widget->location) == 'backend')) {
                $widget->location = strtolower($widget->location);
            } else {
                $widget->location = 'frontend';
            }

            $widget->save();
        }
    }

    /**
     * Delete old widget
     *
     * @param array $allWidget
     */
    protected final function deleteOldWidget($allWidget = [])
    {
        if (count($allWidget)) {
            $oldWidget = CoreWidgets::find()->toArray();
            $oldWidget = array_column($oldWidget, 'base_name');
            $widgetMustDelete = array_diff($oldWidget, $allWidget);
            if (count($widgetMustDelete)) {
                foreach ($widgetMustDelete as $w) {
                    $tmp = CoreWidgets::findFirst(['conditions' => 'base_name = ?0', 'bind' => [$w]]);
                    if ($tmp) {
                        $tmp->delete();
                    }
                }
            }
        }
    }
} 