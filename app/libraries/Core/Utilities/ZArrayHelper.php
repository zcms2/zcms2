<?php

namespace ZCMS\Core\Utilities;

/**
 * ZCMS Array Helper
 *
 * @package   ZCMS\Core
 * @since     0.0.1
 */
class ZArrayHelper
{

    /**
     * Check array one
     *
     * @param $array
     * @return bool
     */
    public static function checkArrayOne($array)
    {
        if (is_array($array)) {
            foreach ($array as $item) {
                if (is_array($item)) return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Function to convert array to integer values
     *
     * @param   array &$array The source array to convert
     * @param   mixed $default A default value (int|array) to assign if $array is not an array
     *
     * @return  void
     */
    public static function toInteger(&$array, $default = null)
    {
        if (is_array($array)) {
            foreach ($array as $i => $v) {
                $array[$i] = intval($v);
            }
        } else {
            if ($default === null) {
                $array = [];
            } elseif (is_array($default)) {
                self::toInteger($default, null);
                $array = $default;
            } else {
                $array = [(int)$default];
            }
        }
    }

    /**
     * Multidimensional array safe unique test
     *
     * @param   array $myArray The array to make unique.
     *
     * @return  array
     *
     * @see     http://php.net/manual/en/function.array-unique.php
     */
    public static function arrayUnique($myArray)
    {
        if (!is_array($myArray)) {
            return $myArray;
        }

        foreach ($myArray as &$myValue) {
            $myValue = serialize($myValue);
        }

        $myArray = array_unique($myArray);

        foreach ($myArray as &$myValue) {
            $myValue = unserialize($myValue);
        }

        return $myArray;
    }

    /**
     * Utility function to map an array to a stdClass object.
     *
     * @param   array &$array The array to map.
     * @param   string $class Name of the class to create
     *
     * @return  object   The object mapped from the given array
     *
     */
    public static function toObject(&$array, $class = 'stdClass')
    {
        $obj = null;

        if (is_array($array)) {
            $obj = new $class;

            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    $obj->$k = self::toObject($v, $class);
                } else {
                    $obj->$k = $v;
                }
            }
        }
        return $obj;
    }
}