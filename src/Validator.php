<?php

namespace CraigPaul\Moneris;

class Validator
{
    /**
     * @param array $array
     *
     * @return bool
     */
    public static function isEmpty(array $array = [])
    {
        return empty($array);
    }

    /**
     * @param array $array
     * @param string $key
     *
     * @return bool
     */
    public static function set(array $array, $key = '')
    {
        return isset($array[$key]);
    }
}
