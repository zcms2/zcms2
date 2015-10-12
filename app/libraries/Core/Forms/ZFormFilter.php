<?php

namespace ZCMS\Core\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Class ZFormFilter
 *
 * @package ZCMS\Core\Forms
 */
class ZFormFilter
{
    /**
     * @var \Phalcon\Forms\Form
     */
    public $_form;

    /**
     * @var array
     */
    public $_options;

    /**
     * Construct
     *
     * @param array $options
     * @param array $filter
     */
    public function __construct($options = [], $filter = [])
    {
        $this->_form = new Form();
        $this->_options = $options;
        foreach ($this->_options as $option) {
            $option['type'] = strtoupper($option['type']);
            if (!isset($option['attributes'])) {
                $option['attributes'] = [];
            }
            if (!isset($option['attributes']['value'])) {
                if (isset($filter[$option['name']])) {
                    $option['attributes']['value'] = $filter[$option['name']];
                }

            }
            if (isset($filter[$option['name'] . '_from'])) {
                $option['attributes']['value_from'] = $filter[$option['name'] . '_from'];
            }
            if (isset($filter[$option['name'] . '_to'])) {
                $option['attributes']['value_to'] = $filter[$option['name'] . '_to'];
            }

            if ($option['type'] == 'SELECT' || $option['type'] == 'MULTIPLESELECT') {
                $this->{'add' . $option['type'] . 'Element'}($option['name'], $option['value'], $option['attributes']);
            } else {
                $this->{'add' . $option['type'] . 'Element'}($option['name'], $option['attributes']);
            }
        }
    }

    /**
     * Add text element
     *
     * @param string $name
     * @param array $attributes
     */
    public function addTextElement($name, $attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control input-sm zcms-form-filter';
        } else {
            $attributes['class'] .= ' date-picker form-control input-sm zcms-form-filter';
        }
        $element = new Text($name, $attributes);
        $this->_form->add($element);
    }

    /**
     * Add select element
     *
     * @param string $name
     * @param object|array $options
     * @param array $attributes
     */
    public function addSelectElement($name, $options, $attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control input-sm zcms-form-filter';
        } else {
            $attributes['class'] .= ' form-control input-sm zcms-form-filter';
        }
        $element = new Select($name, $options, $attributes);
        $this->_form->add($element);
    }

    /**
     * Multi select
     *
     * @param $name
     * @param $options
     * @param $attributes
     */
    public function addMultipleSelectElement($name, $options, $attributes)
    {
        if (!isset($attributes['multiple'])) {
            $attributes['multiple'] = 'multiple';
        }
        $this->addSelectElement($name, $options, $attributes);
    }

    /**
     * Add date range element
     *
     * @param string $name
     * @param array $attributes
     */
    public function addDateRangeElement($name, $attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control date-picker input-sm zcms-form-filter';
        } else {
            $attributes['class'] .= ' form-control date-picker input-sm zcms-form-filter';
        }
        //Add attribute date view mode
        $attributes['data-date-viewmode'] = 'years';

        //Add attribute date format
        //$attributes['data-date-format'] = 'yyyy-mm-dd';
        $attributes['data-date-format'] = 'dd-mm-yyyy';

        //Add placeholder
        $attributes['placeholder'] = __('gb_date_from');
        $attributes['value'] = $attributes['value_from'];
        $elementFrom = new Text($name . '_from', $attributes);

        //Add placeholder
        $attributes['placeholder'] = __('gb_date_to');
        $attributes['value'] = $attributes['value_to'];
        $elementTo = new Text($name . '_to', $attributes);

        $this->_form->add($elementFrom);
        $this->_form->add($elementTo);
    }

    /**
     * Add date from to element
     *
     * @param string $name
     * @param array $attributes
     */
    public function addDateElement($name, $attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control date-picker input-sm zcms-form-filter';
        } else {
            $attributes['class'] .= ' form-control date-picker input-sm zcms-form-filter';
        }
        //Add attribute date view mode
        $attributes['data-date-viewmode'] = 'years';

        //Add attribute date format
        $attributes['data-date-format'] = __('gb_full_date_format');

        //Add placeholder
        $element = new Text($name, $attributes);

        $this->_form->add($element);
    }

    /**
     * Add range price element
     *
     * @param string $name
     * @param array $attributes
     */
    public function addPriceRangeElement($name, $attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control input-sm zcms-form-filter';
        } else {
            $attributes['class'] .= ' form-control input-sm zcms-form-filter';
        }

        //Add placeholder
        $attributes['placeholder'] = __('gb_price_from');
        $attributes['value'] = $attributes['value_from'];
        $elementPriceFrom = new Text($name . '_from', $attributes);

        //Add placeholder
        $attributes['placeholder'] = __('gb_price_to');
        $attributes['value'] = $attributes['value_to'];
        $elementPriceTo = new Text($name . '_to', $attributes);

        $this->_form->add($elementPriceFrom);
        $this->_form->add($elementPriceTo);
    }

    /**
     * Add range number element
     *
     * @param string $name
     * @param array $attributes
     */
    public function addNumberRangeElement($name, $attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control input-sm zcms-form-filter';
        } else {
            $attributes['class'] .= ' form-control input-sm zcms-form-filter';
        }

        //Add placeholder
        $attributes['placeholder'] = __('gb_number_from');
        $attributes['value'] = $attributes['value_from'];
        $elementPriceFrom = new Text($name . '_from', $attributes);

        //Add placeholder
        $attributes['placeholder'] = __('gb_number_to');
        $attributes['value'] = $attributes['value_to'];
        $elementPriceTo = new Text($name . '_to', $attributes);

        $this->_form->add($elementPriceFrom);
        $this->_form->add($elementPriceTo);
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->_form;
    }
}