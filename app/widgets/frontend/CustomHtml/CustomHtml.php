<?php

use Phalcon\Tag;
use ZCMS\Core\ZWidget;

/**
 * Class CustomHtml_Widget
 */
class CustomHtml_Widget extends ZWidget
{
    /**
     * @param int $id
     * @param array $widgetInfo
     * @param array $options
     */
    public function __construct($id = null, $widgetInfo = null, $options = null)
    {
        $options = array(
            'title' => '',
            'value' => ''
        );

        parent::__construct($id, $widgetInfo, $options);
    }

    /**
     * Backend form
     *
     * @return string
     */
    public function form()
    {
        $title = isset($this->options->title) ? $this->options->title : "";
        $content = isset($this->options->value) ? $this->options->value : "";

        $form = '<p><label for="' . $this->getFieldId('title') . '">' . __('gb_title') . '</label>';
        $form .= Tag::textField([
            $this->getFieldName('title'),
            'class' => 'form-control input-sm',
            'value' => $title
        ]);
        $form .= '</p>';
        $form .= '<p><label for="' . $this->getFieldId('value') . '">' . __('w_custom_html_form_label_content') . '</label><br/>';
        $form .= Tag::textArea([
            $this->getFieldName('value'),
            'class' => 'form-control input-sm',
            'value' => $content
        ]);
        $form .= '</p>';
        return $form;
    }

    /**
     * Html frontend
     *
     * @return mixed
     */
    public function widget()
    {
        $this->view->setVar('data', $this->options);
    }
}

register_widget('CustomHtml_Widget');