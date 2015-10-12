<?php

namespace ZCMS\Backend\Menu\Forms;

use ZCMS\Core\Forms\ZForm;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class MenuItemForm
 *
 * @package ZCMS\Backend\Menu\Forms
 */
class MenuItemForm extends ZForm
{
    /**
     * @var string
     */
    public $_formName = 'm_menu_form_menu_item';

    /**
     * Init form
     *
     * @param \ZCMS\Core\Models\MenuItems $data
     */
    public function initialize($data = null)
    {
        $name = new Text('name', ['required' => 'required']);
        $name->addValidator(new PresenceOf());
        $this->add($name);

        $class = new Text('class');
        $this->add($class);

        $icon = new Text('icon');
        $this->add($icon);

        $link = new Text('link');
        $this->add($link);

        $thumbnail = new File('thumbnail');
        $this->add($thumbnail);

        $published = new Select('published', [
            '1' => __('gb_yes'),
            '0' => __('gb_no'),
        ], [
            'value' => $data->published = 0 ? 0 : 1
        ]);
        $this->add($published);

        $require_login = new Select('require_login', [
            '0' => __('gb_display_always'),
            '-1' => __('gb_hidden_when_user_login'),
            '1' => __('gb_display_when_user_login')
        ]);
        $this->add($require_login);
    }
}