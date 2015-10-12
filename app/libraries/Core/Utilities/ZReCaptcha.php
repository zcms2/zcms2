<?php

namespace ZCMS\Core\Utilities;

use ReCaptcha\ReCaptcha;

require(ROOT_PATH . '/app/libraries/ReCaptcha/src/autoload.php');

/**
 * Class ZReCaptcha
 *
 * Extends from Google ReCaptcha
 *
 * @package ZCMS\Core\Utilities
 */
class ZReCaptcha extends ReCaptcha
{

}