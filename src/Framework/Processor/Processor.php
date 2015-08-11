<?php

namespace Framework\Processor;


abstract class Processor
{
    abstract public function then( $func );

    private static $me = null;

    public static function getInstance( ) {
        if ( self::$me == null ) self::$me = new static();
        return self::$me;
    }

}