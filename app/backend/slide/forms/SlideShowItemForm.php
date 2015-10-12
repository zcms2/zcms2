<?php

namespace ZCMS\Backend\Slide\Forms;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use ZCMS\Core\Forms\ZForm;

/**
 * Class SlideShowItemForm
 *
 * @package ZCMS\Backend\Slide\Forms
 */
class SlideShowItemForm extends ZForm
{
    /**
     * @var string
     */
    public $_formName = 'm_slide_form_slide_show_item_form';

    /**
     * Init form
     *
     * @param object $data
     */
    public function initialize($data = null)
    {
        $title = new Text('title');
        $title->addValidator(new PresenceOf([
            'message' => __('m_slide_form_slide_show_item_form_please_typing_title')
        ]));
        $this->add($title);

        $link = new Text('link', ['value' => '#']);
        $this->add($link);
        $link->addValidator(new PresenceOf([
            'message' => __('m_slide_form_slide_show_item_form_please_typing_link')
        ]));

        $description = new TextArea('description', ['rows' => 5]);
        $this->add($description);

        $target = new Select('target', [
            '_blank' => __('m_slide_form_slide_show_item_form_select_open_a_new_tab'),
            '' => __('m_slide_form_slide_show_item_form_select_current_tab')
        ], [
            'value' => '_blank'
        ]);
        $this->add($target);

        $published = new Select('published', ['1' => __('gb_published'), '0' => __('gb_unpublished')], ['value' => $data != null ? $data->published : 1]);
        $this->add($published);
    }
}