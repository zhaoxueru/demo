<?php

namespace demo;

use ext\redis_session;

class test
{
    public static $key = [
        'func' => []
    ];

    public static function func()
    {

        redis_session::start();


    }
}