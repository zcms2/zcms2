<?php

namespace ZCMS\Backend\Menu\Forms;

use Phalcon\Forms\Element\TextArea;
use ZCMS\Core\Forms\ZForm;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class MenuItemForm
 *
 * @package ZCMS\Backend\Menu\Forms
 */
class MenuTypeForm extends ZForm
{
    /**
     * @var string
     */
    public $_formName = 'm_menu_form_menu_type';

    /**
     * @var bool
     */
    public $_autoGenerateTranslateHelpLabel = false;

    /**
     * Init form
     *
     * @param \ZCMS\Core\Models\MenuItems $data
     */
    public function initialize($data = null)
    {
        $name = new Text("name");
        $name->addValidator(new PresenceOf());
        $this->add($name);

        $description = new TextArea("description");
        $this->add($description);

        $published = new Select("published", [
            "1" => __("gb_yes"),
            "0" => __("gb_no")
        ]);
        $this->add($published);
    }
}