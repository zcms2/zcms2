<?php

namespace ZCMS\Core\Forms\Element;

use Phalcon\Forms\Element\Select;
use ZCMS\Core\Models\CoreLanguages;

/**
 * Class Languages
 *
 * @package ZCMS\Core\Forms\Element
 */
class Languages extends Select
{
    /**
     * Constructor
     *
     * @param $name
     * @param array $options
     * @param array $attributes
     */
    public function __construct($name, $options = null, $attributes = null)
    {

        if (!isset($attributes['value'])) {
            /**
             * @var CoreLanguages $defaultLanguage
             */
            $defaultLanguage = CoreLanguages::findFirst('is_default = 1');
            $attributes['value'] = $defaultLanguage->language_code;
        }
        $attributes['using'] = [
            'language_code',
            'title'
        ];
        $options = CoreLanguages::find([
            'order' => 'title ASC'
        ]);

        parent::__construct($name, $options, $attributes);
    }
}