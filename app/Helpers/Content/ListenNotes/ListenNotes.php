<?php

namespace App\Helpers\Content\ListenNotes;



class ListenNotes {

    protected static function resolve($name)
    {
        return app()[$name];
    }

    public static function __callStatic($method, $params)
    {
        return self::resolve('ListenNotes')
        ->$method(...$params);
    }

}