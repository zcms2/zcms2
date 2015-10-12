<?php

namespace ZCMS\Backend\System\Controllers;

use ZCMS\Core\ZAdminController;
use ZCMS\Core\Utilities\ZCrypt;
use ZCMS\Core\Models\CoreConfigs;
use ZCMS\Core\Models\CoreLanguages;
use ZCMS\Core\Models\CoreTemplates;
use ZCMS\Backend\System\Forms\ConfigForm;

/**
 * Class ConfigController
 *
 * @package ZCMS\Backend\System\Controllers
 */
class ConfigController extends ZAdminController
{

    /**
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function indexAction()
    {
        //Add tool bar
        $this->_toolbar->addBreadcrumb(['title' => 'm_system_system_manager']);
        $this->_toolbar->addBreadcrumb(['title' => 'm_system_manage_manager']);
        $this->_toolbar->addHeaderPrimary('m_system_manage_manager');
        $this->_toolbar->addHeaderSecond('m_system_system_manager');

        $error_is_writable = false;
        if (!is_writable(APP_DIR . '/backup')) {
            $this->flashSession->error('gb_backup_folder_cannot_be_writable ');
            $error_is_writable = true;
        }

        if (!is_writable(APP_DIR . '/config')) {
            $error_is_writable = true;
            $this->flashSession->error('gb_config_folder_cannot_be_writable ');
        }

        if (!$error_is_writable) {
            $this->_toolbar->addSaveButton('system|config|manage');
        }

        $core_config = CoreConfigs::find();
        $config = [];
        foreach ($core_config as $item) {
            $config[$item->key] = $item->value;
        }
        $config = (object)$config;
        $configForm = new ConfigForm($config);

        $this->view->setVar('configForm', $configForm);

        if ($this->request->isPost()) {
            if ($configForm->isValid($_POST, $config)) {
                $error = 0;
                foreach ($configForm->getElements() as $element) {
                    $name = $element->getName();

                    /**
                     * @var $coreConfig CoreConfigs
                     */
                    $coreConfig = CoreConfigs::findFirst([
                        'conditions' => 'key = ?0',
                        'bind' => [$name]
                    ]);
                    if ($coreConfig && $config->{$name} != '') {
                        if ($coreConfig->scope == 'database') continue;
                        //Crypt value
                        if ($coreConfig->is_crypt_value) {
                            $coreConfig->value = ZCrypt::getInstance()->encrypt($config->{$name});
                        } else {
                            $coreConfig->value = $config->{$name};
                        }

                        if ($coreConfig->save()) {
                            if ($name == 'defaultTemplate') {
                                $coreTemplate = new CoreTemplates();
                                $coreTemplate->setDefaultTemplate($config->{$name}, 'backend');
                            }
                            if ($name == 'defaultTemplate') {
                                $coreTemplate = new CoreTemplates();
                                $coreTemplate->setDefaultTemplate($config->{$name}, 'frontend');
                            }
                            if ($name == 'language') {
                                $coreLanguage = new CoreLanguages();
                                $coreLanguage->setDefaultLanguage($config->{$name});
                            }
                        } else {
                            $error++;
                            $this->setFlashSession($coreConfig->getMessages(), 'warning');
                        }
                    }
                }
                if ($error != 0) {
                    $this->flashSession->warning('m_system_config_message_some_config_cannot_save');
                } else {
                    //$defaultLanguage = CoreLanguage::findFirst('is_default = 1');
                    $this->flashSession->success('m_system_config_message_config_save_successfully');
                    $configFileContent = file_get_contents(APP_DIR . '/config/config.php');
                    if (file_put_contents(APP_DIR . '/backup/config.backup_' . date('Y-m-d') . '_at_' . date('H-i-s') . '.php', $configFileContent)) {

                        $newConfig = [];
                        $sections = $this->getSectionConfig();
                        foreach ($sections as $section) {
                            $itemsInSection = $this->getItemInSectionConfig($section);
                            if ($section == 'system') {
                                foreach ($itemsInSection as $item) {
                                    if (is_numeric($item->value)) {
                                        $item->value = (int)$item->value;
                                    }
                                    $newConfig[$item->key] = $item->value;
                                }
                            } else {
                                foreach ($itemsInSection as $item) {
                                    if (is_numeric($item->value)) {
                                        $item->value = (int)$item->value;
                                    }
                                    $newConfig[$section][$item->key] = $item->value;
                                    if ($section == 'website') {
                                        /**
                                         * @var CoreLanguages $defaultLanguage
                                         */
                                        $defaultLanguage = CoreLanguages::findFirst('is_default = 1');
                                        /**
                                         * @var CoreConfigs $directionConfig
                                         */
                                        $directionConfig = CoreConfigs::findFirst("scope = 'website' AND key = 'direction'");
                                        $newConfig['website']['language'] = $defaultLanguage->language_code;
                                        $newConfig['website']['metakey'] = htmlspecialchars_decode($defaultLanguage->metaKey);
                                        $newConfig['website']['metadesc'] = htmlspecialchars_decode($defaultLanguage->metaDesc);
                                        $newConfig['website']['sitename'] = htmlspecialchars_decode($defaultLanguage->siteName);
                                        $newConfig['website']['direction'] = $directionConfig->key;
                                        $newConfig['website']['country_image'] = $defaultLanguage->image;
                                    }
                                }
                            }
                        }

                        /**
                         * Todo
                         * Add new on feature ZCMS
                         */
                        $newConfig['apc_cache'] = [
                            'apc_prefix' => 'cache',
                            'apc_lifetime' => 1800,
                            'apc_status' => 1
                        ];

                        $var_str = var_export($newConfig, true);
                        $newConfigFileContent = "<?php\n\n return $var_str;\n";
                        if (!file_put_contents(APP_DIR . '/config/config.php', $newConfigFileContent)) {
                            $this->flashSession->warning('gb_config_folder_cannot_be_writable');
                            return false;
                        }
                    } else {

                    }
                }
            } else {
                $this->flashSession->warning('m_system_config_message_please_check_error_field');
                $this->setFlashSession($configForm->getMessages(), 'warning');
                return false;
            }

            $this->view->disable();
            return $this->response->redirect('/admin/system/config');
        }
        return true;
    }

    /**
     * Get section config
     *
     * @return array
     */
    protected function getSectionConfig()
    {
        /**
         * Todo
         * Remove oder scope for Stable Version
         */
        return array_unique(array_column(CoreConfigs::find(['order' => 'scope ASC'])->toArray(), 'scope'));
    }

    /**
     * Get item section
     * @param $scope
     * @return CoreConfigs[]
     */
    protected function getItemInSectionConfig($scope)
    {
        return CoreConfigs::find([
            'columns' => 'key,value,scope',
            'conditions' => "scope = ?0",
            'bind' => [$scope],
            'order' => 'key ASC'
        ]);
    }
}