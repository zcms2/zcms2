<?php

namespace ZCMS\Core\Forms\Element;

use Phalcon\Forms\Element;
use Phalcon\Forms\ElementInterface;
use Phalcon\Tag;

/**
 * Class DateTime
 *
 * @package ZCMS\Core\Forms\Element
 */
class DateTime extends Element implements ElementInterface
{
    /**
     * Renders the element widget
     *
     * @param array $attributes
     * @return string
     */
    public function render($attributes = null)
    {
        if ($attributes == null) {
            $attributes = $this->_attributes;
        }
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' date-time-picker';
            if (strpos($attributes['class'], 'form-control') !== false) {
                $attributes['class'] .= ' form-control';
            }
        } else {
            $attributes['class'] = 'form-control date-time-picker';
        }

        if (!isset($attributes['data-date-format'])) {
            $attributes['data-date-format'] = 'dd-mm-yyyy hh:ii:ss';
        }
        return Tag::textField($this->prepareAttributes($attributes));
    }

}
