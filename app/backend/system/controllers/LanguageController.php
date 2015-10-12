<?php

namespace ZCMS\Backend\System\Controllers;

use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\Models\CoreLanguages;
use ZCMS\Core\Utilities\ZArrayHelper;

/**
 * Class LanguageController
 * @package ZCMS\Backend\System\Controllers
 */
class LanguageController extends ZAdminController
{

    /**
     * @var string PHQL Model
     */
    public $_model = 'ZCMS\Core\Models\CoreLanguages';

    /**
     * @var string Model name in database
     */
    public $_modelBaseName = 'core_languages';

    public function indexAction()
    {
        $this->_toolbar->addCustomButton('system|language|setDefaultLanguage',
            'm_system_language_message_set_default_language',
            '/admin/system/language/setDefaultLanguage/', 'glyphicon glyphicon-star',
            'btn btn-primary btn-sm',
            'return ZCMS.customSubmit(this,\'' . __('m_system_language_message_do_you_wan_set_default_language') . '\',\'' . __('gb_please_select_item_to_set_default_language') . '\');'
        );
        $this->_toolbar->addPublishedButton('system|language|published', '/admin/system/language/publish');
        $this->_toolbar->addUnPublishedButton('system|language|unpublished', '/admin/system/language/unPublish');

        //Add filter
        $this->addFilter('filter_order', 'language_id', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_search', '', 'string');

        //Get all filter
        $filter = $this->getFilter();

        $conditions = [];

        if ($filter['filter_search'] = trim($filter['filter_search'])) {
            $conditions[] = "language_code like '%" . $filter['filter_search'] . "%' OR title like '%" . $filter['filter_search'] . "%'";
        }

        //Get all template
        $items = CoreLanguages::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

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
                'column' => 'language_id',
            ],
            [
                'type' => 'index',
                'title' => '#',
            ],
            [
                'type' => 'text',
                'title' => 'gb_language_title',
                'column' => 'title'
            ],
            [
                'type' => 'text',
                'title' => 'gb_language_code',
                'column' => 'language_code',
                'class' => 'text-center col-language'
            ],
            [
                'type' => 'action',
                'title' => 'gb_default_language',
                'column' => 'is_default',
                'link_prefix' => 'id',
                'class' => 'text-center col-language',
                'action' => [
                    [
                        'condition' => '==',
                        'condition_value' => '1',
                        'link' => '/admin/system/language/#',
                        'link_title' => 'gb_default_language',
                        'access' => 1,
                        'icon_class' => 'glyphicon glyphicon-star orange',
                    ],
                    [
                        'condition' => '==',
                        'condition_value' => '0',
                        'link' => '/admin/system/language/setDefaultLanguage/',
                        'link_title' => 'm_system_language_message_set_default_language',
                        'access' => $this->acl->isAllowed('system|language|setDefault'),
                        'icon_class' => 'glyphicon glyphicon-star grey',
                    ]
                ]
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'column' => 'published',
                'access' => $this->acl->isAllowed('system|language|published'),
                'link' => '/admin/system/language/'
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'language_id'
            ]
        ]);
    }

    /**
     * Published item action
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     */
    public function publishAction($id = null, $redirect = null, $log = true)
    {
        $extraQuery = ', updated_by = ' . $this->_user['id'] . ", updated_at = '" . date("Y-m-d H:i:s") . "'";
        if ($id) {
            $id = intval($id);
            $ids[] = $id;
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
        }
        if (is_array($ids)) {
            ZArrayHelper::toInteger($ids);
            $query = "UPDATE {$this->_modelBaseName} SET published = 1 " . $extraQuery . " WHERE id IN (" . implode(',', $ids) . ")";
            $this->db->execute($query);
            $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_published', ["1" => $this->db->affectedRows()]));
        }

        if ($redirect) {
            $this->response->redirect($redirect);
        } else {
            $this->response->redirect('/admin/' . $this->_module . '/' . $this->_controller . '/');
        }
    }

    /**
     * Unpublished item action
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     */
    public function unPublishAction($id = null, $redirect = null, $log = true)
    {
        $extraQuery = ', updated_by = ' . $this->_user['id'] . ", updated_at = '" . date("Y-m-d H:i:s") . "'";
        if ($id) {
            $id = intval($id);
            $ids[] = $id;
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
        }
        if (is_array($ids)) {
            ZArrayHelper::toInteger($ids);
            $query = "UPDATE {$this->_modelBaseName} SET published = 0 " . $extraQuery . " WHERE id IN (" . implode(',', $ids) . ") AND is_default = 0";
            $this->db->execute($query);
            $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_unpublished', ["1" => $this->db->affectedRows()]));
        }

        if ($redirect) {
            $this->response->redirect($redirect);
        } else {
            $this->response->redirect('/admin/' . $this->_module . '/' . $this->_controller . '/');
        }
    }

    /**
     * Set default language
     *
     * @param $id
     */
    public function setDefaultLanguageAction($id = null)
    {
        if ($id) {
            $id = intval($id);
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
            $id = array_shift($ids);
        }
        /**
         * @var CoreLanguages $item
         */
        $item = CoreLanguages::findFirst($id);
        if ($id > 0 && $item) {
            $this->modelsManager->createQuery('UPDATE ' . $this->_model . ' SET is_default = 0')->execute();
            $item->is_default = 1;
            $item->published = 1;
            if ($item->save()) {
                $this->flashSession->success(__('m_system_language_set_default_language_successfully', ['1' => $item->title]));
            } else {
                $this->flashSession->error(__('m_system_language_set_default_language_error'));
            }
        } else {
            $this->flashSession->error(__('m_system_language_set_default_language_error'));
        }
        $this->response->redirect('/admin/system/language/');
    }
}