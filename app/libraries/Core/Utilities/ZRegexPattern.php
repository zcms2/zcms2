<?php

namespace ZCMS\Core\Utilities;

/**
 * Class ZRegexPattern
 *
 * @package ZCMS\Core\Utilities
 */
class ZRegexPattern
{
    const INT = '/-?[0-9]+/';
    const FLOAT = '/-?[0-9]+(\.[0-9]+)?/';
    const DOUBLE = '/-?[0-9]+(\.[0-9]+)?/';
    const WORD = '/[^A-Z_]/i';
    const ALNUM = '/[^A-Z0-9]/i';
    const BASE64 = '/[^A-Z0-9\/+=]/i';
    const PATH = '//^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/';
    const USERNAME = '/[\x00-\x1F\x7F<>"\'%&]/';
    const CMD = '/[^A-Z0-9_\.-]/i';
}