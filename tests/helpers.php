<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

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

if (!function_exists('mock_handler')) {
    /**
     * @param $stub
     *
     * @return \GuzzleHttp\Client
     */
    function mock_handler($stub)
    {
        $mock = new MockHandler([
            new Response(200, [], $stub)
        ]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}