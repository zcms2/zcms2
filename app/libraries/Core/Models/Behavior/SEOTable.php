<?php

namespace ZCMS\Core\Models\Behavior;

/**
 * Class SEOTable
 *
 * @package ZCMS\Core\Models\Behavior
 */
trait SEOTable
{

    /**
     * @var string
     */
    public $meta_desc = '';

    /**
     * @var string
     */
    public $meta_keywords = '';

    /**
     * @var array
     */
    public $metadata = '';
}