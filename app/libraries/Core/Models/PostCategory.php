<?php

namespace ZCMS\Core\Models;

/**
 * Class PostCategory
 *
 * @package ZCMS\Core\Models
 */
class PostCategory extends Categories
{
    public $module = 'content';

    public function getSource()
    {
        return 'categories';
    }
}