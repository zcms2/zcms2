<?php

namespace ZCMS\Backend\System\Forms;

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Regex;
use ZCMS\Core\Forms\ZForm;
use ZCMS\Core\Models\CoreConfigs;
use ZCMS\Core\Models\CoreLanguages;
use ZCMS\Core\Models\CoreTemplates;
use ZCMS\Core\ZTranslate;

/**
 * Class ConfigForm
 *
 * @package ZCMS\Backend\System\Forms
 */
class ConfigForm extends ZForm
{
    /**
     * @param CoreConfigs $data
     */
    public function initialize($data = null)
    {
        $allConfig = CoreConfigs::find()->toArray();
        $isCryptOfAllKey = [];
        foreach ($allConfig as $conf) {
            $isCryptOfAllKey[$conf['key']] = $conf['is_crypt_value'];
        }

        if ($data != null) {
            $allKeyData = get_object_vars($data);
            foreach ($allKeyData as $key => $value) {
                if ($isCryptOfAllKey[$key] == 1) {
                    $data->{$key} = '';
                }
            }
        }

        //Section Website
        $debug = new Select('debug', [0 => __('gb_off'), 1 => __('gb_on')]);
        $debug->addValidator(new InclusionIn(['domain' => ['0', '1']]));
        $this->add($debug);

        $baseUri = new Text('baseUri');
        $this->add($baseUri);

        $metaDesc = new TextArea("metadesc", ['rows' => 4]);
        $this->add($metaDesc);

        $metaKey = new TextArea("metakey");
        $this->add($metaKey);

        $siteName = new Text("sitename");
        $this->add($siteName);

        $direction = new Select('direction', [
            'ltr' => 'Left to right',
            'rtl' => 'Right to Left'
        ]);
        $this->add($direction);

        $timezone = new Select('timezone', array_combine(timezone_identifiers_list(), timezone_identifiers_list()));
        $this->add($timezone);

        //$CoreLanguages = CoreLanguages::find(['columns' => 'language_code, title', 'order' => 'language_code']);
        $CoreLanguages = CoreLanguages::find(['order' => 'language_code']);
        $language = new Select('language', $CoreLanguages, ['using' => ['language_code', 'title']]);
        $this->add($language);

        $limit = new Text('limit');
        $limit->addValidator(new Between([
            'minimum' => 0,
            'maximum' => 100,
            'message' => 'm_system_module_message_the_limit_must_be_between_1_100'
        ]));
        $this->add($limit);

        $media_limit = new Text('media_limit');
        $media_limit->addValidator(new Regex([
            'pattern' => '/[0-9]+(\.[0-9]+)?/',
            'message' => 'm_system_config_message_this_field_must_be_a_number'
        ]));
        $media_limit->addValidator(new Between([
            'minimum' => 0,
            'maximum' => 100,
            'message' => 'm_system_module_message_the_media_limit_must_be_between_1_100'
        ]));
        $this->add($media_limit);

        $feed_limit = new Text('feed_limit');
        $feed_limit->addValidator(new Regex([
            'pattern' => '/[0-9]+(\.[0-9]+)?/',
            'message' => 'm_system_config_message_this_field_must_be_a_number'
        ]));
        $feed_limit->addValidator(new Between([
            'minimum' => 0,
            'maximum' => 100,
            'message' => 'm_system_module_message_the_media_limit_must_be_between_1_100'
        ]));
        $this->add($feed_limit);

        //Section memcache
        $mem_cache_status = new Select('mem_cache_status', [0 => __('gb_off'), 1 => __('gb_on')]);
        $mem_cache_status->addValidator(new InclusionIn(['domain' => ['0', '1']]));
        $this->add($mem_cache_status);

        $mem_cache_host = new Text('mem_cache_host');
        $this->add($mem_cache_host);

        $mem_cache_lifetime = new Text('mem_cache_lifetime');
        $this->add($mem_cache_lifetime);

        $mem_cache_prefix = new Text('mem_cache_prefix');
        $this->add($mem_cache_prefix);

        $mem_cache_port = new Text('mem_cache_port');
        $mem_cache_port->addValidator(new Regex([
            'pattern' => '/[0-9]+(\.[0-9]+)?/',
            'message' => 'm_system_module_message_the_memcache_port_must_be_a_number'
        ]));
        $this->add($mem_cache_port);

        //Section apc cache
        $apc_prefix = new Text('apc_prefix');
        $this->add($apc_prefix);

        $apc_lifetime = new Text('apc_lifetime');
        $this->add($apc_lifetime);

        $apc_status = new Select('apc_status', [0 => __('gb_off'), 1 => __('gb_on')]);
        $apc_status->addValidator(new InclusionIn(['domain' => ['0', '1']]));
        $this->add($apc_status);

        //Section Mail
        /**
         * <code>
         * <select id="mail_type" name="mail_type" class="form-control">
         *    <option value="mail">Mail</option>
         *    <option selected="selected" value="smtp">SMTP</option>
         *    <option value="sendmail">SendMail</option>
         * </select>
         * </code>
         */
        $mail_type = new Select('mail_type', [
                'mail' => __('m_system_config_label_mail_type_value_mail'),
                'smtp' => __('m_system_config_label_mail_type_value_smtp'),
                'sendmail' => __('m_system_config_label_mail_type_value_sendmail')]
        );
        $mail_type->addValidator(new InclusionIn(['domain' => ['mail', 'smtp', 'sendmail']]));
        $this->add($mail_type);

        $mail_from = new Text('mail_from');
        $this->add($mail_from);

        $from_name = new Text('from_name');
        $this->add($from_name);

        $send_mail = new Text('send_mail');
        $this->add($send_mail);

        $smtp_user = new Text('smtp_user');
        $this->add($smtp_user);

        $smtp_pass = new Password('smtp_pass');
        $this->add($smtp_pass);

        $smtp_host = new Text('smtp_host');
        $this->add($smtp_host);

        $smtp_secure = new Select('smtp_secure',
            [
                'ssl' => 'SSL',
                'tsl' => 'TSL',
            ]
        );
        $smtp_secure->addValidator(new InclusionIn(['domain' => ['ssl', 'tsl']]));
        $this->add($smtp_secure);

        $smtp_port = new Text('smtp_port');
        $smtp_port->addValidator(new Regex([
            'pattern' => '/[0-9]+(\.[0-9]+)?/',
            'message' => 'm_system_module_message_the_smtp_port_must_be_a_number'
        ]));
        $this->add($smtp_port);

        $smtp_auth = new Text('smtp_auth');
        $smtp_auth->addValidator(new Regex([
            'pattern' => '/[0-9]+(\.[0-9]+)?/',
            'message' => 'm_system_module_message_the_smtp_auth_must_be_a_number'
        ]));
        $this->add($smtp_auth);

        //Section log
        $log = new Select('log',
            [
                '0' => __('gb_off'),
                '1' => __('gb_on'),
            ]
        );
        $log->addValidator(new InclusionIn(['domain' => ['0', '1']]));
        $this->add($log);

        $log_type = new Select('log_type',
            [
                'file' => __('m_system_config_label_database_file'),
                'database' => __('m_system_config_label_database')
            ]
        );
        $log_type->addValidator(new InclusionIn(['domain' => ['file', 'database']]));
        $this->add($log_type);


        $auth_lifetime = new Text('auth_lifetime');
        $auth_lifetime->addValidator((new Regex([
            'pattern' => '/[0-9]+(\.[0-9]+)?/',
            'message' => 'm_system_module_message_the_auth_lifetime_must_be_a_number'
        ])));
        $this->add($auth_lifetime);

        //Section Template

        //Add translation template
        ZTranslate::getInstance()->addTemplateLang(get_child_folder(APP_DIR . '/templates/backend'));
        ZTranslate::getInstance()->addTemplateLang(get_child_folder(APP_DIR . '/templates/frontend'), 'frontend');
        //Backend default template
        $backendTemplateArray = CoreTemplates::find("location = 'backend'")->toArray();
        $backendTemplateBaseName = array_column($backendTemplateArray, 'base_name');
        $backendTemplate = array_combine($backendTemplateBaseName, array_map('__', array_column($backendTemplateArray, 'name')));
        $defaultTemplate = new Select('defaultTemplate', $backendTemplate);
        $defaultTemplate->addValidator(new InclusionIn(['domain' => $backendTemplateBaseName]));
        $this->add($defaultTemplate);

        //Backend compile template
        $compileTemplate = new Select('compileTemplate',
            [
                '0' => __('gb_off'),
                '1' => __('gb_on'),
            ]
        );
        $compileTemplate->addValidator(new InclusionIn(['domain' => ['0', '1']]));
        $this->add($compileTemplate);

        //Frontend default template
        $frontendTemplateArray = CoreTemplates::find("location = 'frontend'")->toArray();
        $frontendTemplateBaseName = array_column($frontendTemplateArray, 'base_name');
        $frontendTemplate = array_combine($frontendTemplateBaseName, array_map('__', array_column($frontendTemplateArray, 'name')));
        $defaultTemplate = new Select('defaultTemplate', $frontendTemplate);
        $defaultTemplate->addValidator(new InclusionIn(['domain' => $frontendTemplateBaseName]));
        $this->add($defaultTemplate);

        //Frontend compile template
        $compileTemplate = new Select('compileTemplate',
            [
                '0' => __('gb_off'),
                '1' => __('gb_on'),
            ]
        );
        $compileTemplate->addValidator(new InclusionIn(['domain' => ['0', '1']]));
        $this->add($compileTemplate);

        //Shipping
        $shipping = new Text('sender_id');
        $this->add($shipping);

        $shipping = new Text('sender_name');
        $this->add($shipping);

        $shipping = new Text('sender_contact_name');
        $this->add($shipping);

        $shipping = new Text('sender_contact_phone');
        $this->add($shipping);

        $shipping = new Text('sender_address_1');
        $this->add($shipping);

        $shipping = new Text('sender_address_2');
        $this->add($shipping);

        $shipping = new Text('sender_town');
        $this->add($shipping);

        $shipping = new Text('sender_state');
        $this->add($shipping);

        $shipping = new Text('sender_post_code');
        $this->add($shipping);

        $shipping = new Text('sender_country');
        $this->add($shipping);

        $shipping = new Text('toll_slid');
        $this->add($shipping);

        $shipping = new Text('toll_service_name');
        $this->add($shipping);

        $shipping = new Text('toll_service_code');
        $this->add($shipping);

        $shipping = new Text('toll_email_transmission');
        $this->add($shipping);

        $shipping = new Text('toll_system_transmission_id');
        $this->add($shipping);

        $shipping = new Text('line_weight');
        $this->add($shipping);

        $shipping = new Text('charge_account');
        $this->add($shipping);

        //Payment get way
        $merchant_number = new Text('merchant_number');
        $this->add($merchant_number);

        $merchant_number = new Text('merchant_username');
        $this->add($merchant_number);

        $merchant_number = new Password('merchant_password');
        $this->add($merchant_number);

        $merchant_number = new Text('merchant_referrence');
        $this->add($merchant_number);

        $merchant_number = new Text('link_authorize');
        $this->add($merchant_number);

        $merchant_number = new Text('link_pay_request');
        $this->add($merchant_number);

        $merchant_number = new Text('link_verify');
        $this->add($merchant_number);

        $merchant_number = new Text('link_pay_shop');
        $this->add($merchant_number);

        $payment_return_url = new Text('return_url');
        $this->add($payment_return_url);
    }
}