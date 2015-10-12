<?php

namespace ZCMS\Backend\Slide\Forms;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use ZCMS\Core\Forms\ZForm;

/**
 * Class SlideShowForm
 *
 * @package ZCMS\Backend\Slide\Forms
 */
class SlideShowForm extends ZForm
{
    /**
     * @var string
     */
    public $_formName = 'm_slide_form_slide_show_form';

    /**
     * Init form
     *
     * @param \ZCMS\Core\Models\SlideShows $data
     */
    public function initialize($data = null)
    {
        $title = new Text('title', ['required' => 'required']);
        $title->addValidator(new PresenceOf([
            'message' => 'Please typing title'
        ]));
        $this->add($title);

        $alias = new Text('alias');
        $this->add($alias);

        $description = new TextArea('description', ['rows' => 5]);
        $this->add($description);

        $published = new Select('published', ['1' => __('gb_published'), '0' => __('gb_unpublished')], ['value' => $data != null ? $data->published : 1]);
        $this->add($published);
    }
}