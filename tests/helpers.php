<?php

if (!function_exists('dd')) {
    /**
     * @param array ...$args
     */
    function dd(...$args)
    {
        call_user_func_array('dump', $args);

        die();
    }
}