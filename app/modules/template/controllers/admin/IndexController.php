<?php

namespace ZCMS\Modules\Template\Controllers\Admin;

use ZCMS\Core\Models\CoreTemplates;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\ZTranslate;

/**
 * Class IndexController
 *
 * @package ZCMS\Modules\Template\Controllers
 * @version 0.0.1
 */
class IndexController extends ZAdminController
{

    /**
     * Index action
     *
     * Display list template
     */
    public function indexAction()
    {
        //Add template language
        $this->_addTemplateLang();
        //Update all template frontend
        $this->_updateAllTemplate('frontend');

        //Add toolbar button
        $this->_toolbar->addNewButton('install');

        //Add filter
        $this->addFilter('filter_order', 'template_id', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_search', '', 'string');

        //Get all filter
        $filter = $this->getFilter();
        //Set view filter
        $this->view->setVar('_filter', $filter);

        $conditions = [];

        if (trim($filter['filter_search'])) {
            $conditions[] = "name like '%" . trim($filter['filter_search']) . "%'";
        }

        /**
         * @var CoreTemplates[] $items
         */
        $items = CoreTemplates::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

        if (!count($items)) {
            $this->flashSession->notice(__('m_template_notice_there_are_no_template_matching_your_query'));
        }

        $currentPage = $this->request->getQuery('page', 'int', 1);
        $paginationLimit = $this->config->pagination->limit;

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationModel($items, $paginationLimit, $currentPage));

        //Set view layout
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'template_id'
            ],
            [
                'type' => 'text',
                'title' => 'gb_template_name',
                'column' => 'name',
                'translation' => true,
            ],
            [
                'type' => 'text',
                'title' => 'gb_description',
                'column' => 'description',
                'translation' => true,
            ],
            [
                'type' => 'text',
                'title' => __('gb_version'),
                'class' => 'text-center',
                'column' => 'version'
            ],
            [
                'type' => 'text',
                'title' => 'gb_author',
                'class' => 'text-center',
                'column' => 'author'
            ],
            [
                'type' => 'action',
                'title' => 'gb_active',
                'column' => 'published',
                'link_prefix' => 'template_id',
                'class' => 'text-center col-published',
                'action' => [
                    [
                        'condition' => '==',
                        'condition_value' => '1',
                        'link' => '/admin/template/index/#',
                        'link_title' => 'gb_default_language',
                        'access' => 1,
                        'icon_class' => 'glyphicon glyphicon-star orange',
                    ],
                    [
                        'condition' => '==',
                        'condition_value' => '0',
                        'link' => '/admin/template/index/publish/',
                        'link_title' => 'm_system_language_message_set_default_language',
                        'access' => $this->acl->isAllowed('system|language|published'),
                        'icon_class' => 'glyphicon glyphicon-star grey',
                    ]
                ]
            ],
            [
                'type' => 'text',
                'title' => 'gb_location',
                'class' => 'text-center',
                'column' => 'location',
                'label' => [
                    [
                        'condition' => '==',
                        'condition_value' => 'admin',
                        'class' => 'label label-sm label-success',
                        'text' => 'gb_backend'
                    ],
                    [
                        'condition' => '!=',
                        'condition_value' => 'admin',
                        'class' => 'label label-sm label-warning',
                        'text' => 'gb_frontend'
                    ]
                ],
                'translation' => true,
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'template_id'
            ]
        ]);
    }

    /**
     * Update template information
     *
     * @param string $location Value backend|frontend
     */
    private function _updateAllTemplate($location)
    {
        if ($location === 'frontend' || $location === 'admin') {
            $templates = get_child_folder(ROOT_PATH . '/app/templates/' . $location . '/');
            if (count($templates)) {
                $templateTmp = [];
                foreach ($templates as $template) {
                    $templateTmp[] = "'" . $template . "'";
                }
                /**
                 * @var CoreTemplates[] $templateMustDelete
                 */
                $templateMustDelete = CoreTemplates::find([
                    'conditions' => 'base_name NOT IN(' . implode(',', $templateTmp) . ") AND location='" . $location . "'"
                ]);

                if (count($templateMustDelete) > 0) {
                    foreach ($templateMustDelete as $tMD) {
                        if (method_exists($tMD, "delete")) {
                            $tMD->delete();
                        }
                    }
                }

                foreach ($templates as $template) {
                    $pathTemplate = ROOT_PATH . '/app/templates/' . $location . '/' . $template . '/template.json';
                    if ($resource = check_template($pathTemplate)) {
                        $templateObject = CoreTemplates::findFirst('base_name ="' . $template . '" AND location = "' . $location . '"');
                        if (!$templateObject) {
                            $templateObject = new CoreTemplates();
                            $templateObject->base_name = $template;
                            $templateObject->published = 0;
                            $templateObject->location = $location;
                        }
                        $templateObject->name = $resource['name'];
                        $templateObject->uri = $resource['uri'];
                        $templateObject->author = $resource['author'];
                        $templateObject->authorUri = $resource['authorUri'];
                        $templateObject->version = $resource['version'];
                        $templateObject->tag = $resource['tag'];
                        $templateObject->description = $resource['description'];
                        if (!$templateObject->save()) {
                            $this->flashSession->error(__('m_template_notice_not_update_template', ['1' => $templateObject->name, '2' => '$location', '3' => ROOT_PATH . '/app/templates/{$location}/' . $templateObject->base_name . '/template.json']));
                        };
                    } else {
                        $this->flashSession->error(__('m_template_notice_not_update_template', ['1' => 'Base name: ' . $template, '2' => '$location', '3' => ROOT_PATH . '/app/templates/{$location}/' . $template . '/template.json']));
                    }
                }
                /**
                 * @var CoreTemplates[] $templatePublished
                 */
                $templatePublished = CoreTemplates::find('published = 1 AND location="' . $location . '"');
                if (!count($templatePublished)) {
                    /**
                     * @var CoreTemplates $templateDefault
                     */
                    $templateDefault = CoreTemplates::findFirst('base_name = "default" AND location="frontend"');
                    if ($templateDefault) {
                        $templateDefault->published = 1;
                        $templateDefault->save();
                    }
                }
            }
        }
    }

    /**
     * Published template
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     * @return \Phalcon\Http\ResponseInterface|void
     */
    public function publishAction($id = null, $redirect = null, $log = true)
    {
        //Add template language
        $this->_addTemplateLang();

        $id = (int)$id;
        /**
         * @var CoreTemplates $templateMustPublish
         */
        $templateMustPublish = CoreTemplates::findFirst($id);
        if ($templateMustPublish) {
            $query = "UPDATE core_templates SET published = 0 WHERE location = '{$templateMustPublish->location}'";
            $this->db->execute($query);
            $templateMustPublish->published = 1;
            $templateMustPublish->save();
            file_put_contents(ROOT_PATH . '/app/' . $templateMustPublish->location . '/index.volt', '{% extends "../../../templates/' . $templateMustPublish->location . '/' . $templateMustPublish->base_name . '/index.volt" %}');
            if ($templateMustPublish->location == 'frontend') {
                //Do something
            } elseif ($templateMustPublish->location == 'admin') {
                //Do something
            }
            $this->flashSession->success(__('m_template_notice_template_is_active', ['1' => __($templateMustPublish->name), '2' => $templateMustPublish->location]));
        } else {
            $this->flashSession->error(__('m_template_notice_template_not_exists'));
        }
        return $this->response->redirect('/admin/template/');
    }

    /**
     * Add template language (backend and frontend)
     */
    private function _addTemplateLang()
    {
        $template = ZTranslate::getInstance('admin');
        $templatesPath = ROOT_PATH . '/app/templates/backend/languages/' . $this->config->website->language . '/' . $this->config->website->language . '.php';
        $template->addLang($templatesPath);

        $templates = get_child_folder(ROOT_PATH . '/app/templates/frontend/');
        $template->addTemplateLang($templates, 'frontend');
    }
}