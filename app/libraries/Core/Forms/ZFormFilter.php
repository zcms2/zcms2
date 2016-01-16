<?php

namespace ZCMS\Core\Forms;

use Phalcon\Forms\Element\Hidden;
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
     * @param array $filterOptions
     */
    public function __construct($options = [], $filter = [], $filterOptions = [])
    {
        $this->_form = new Form();
        $this->_options = $options;
        if (isset($filterOptions['filter_order'])) {
            $filterOrder = new Hidden('filter_order', ['value' => $filter['filter_order'], 'data-default' => $filterOptions['filter_order']['value']]);
            $this->_form->add($filterOrder);
        }
        if (isset($filterOptions['filter_order_dir'])) {
            $filterOrderDir = new Hidden('filter_order_dir', ['value' => $filter['filter_order_dir'], 'data-default' => $filterOptions['filter_order_dir']['value']]);
            $this->_form->add($filterOrderDir);
        }
        foreach ($this->_options as $option) {
            $option['type'] = strtoupper($option['type']);
            if (!isset($option['attributes'])) {
                $option['attributes'] = [];
            }
//            if(isset($filterOptions[$option['name']]) && $filterOptions[$option['name']]['value']){
//                $option['attributes']['data-default'] = $filterOptions[$option['name']]['value'];
//            }
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
            $attributes['class'] .= ' form-control input-sm zcms-form-filter';
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