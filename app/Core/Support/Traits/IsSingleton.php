<?php

namespace NikoGin\Core\Support\Traits;

trait IsSingleton {
    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self {
        if ( static::$instance === null ) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
