<?php

namespace Framework\Processor;

use Framework\Exception\FrameworkException;

class PostProcessor extends Processor
{
    public function then( $func ) {

        if ( is_callable( $func ) ) {
            $function();
        }
        if ( is_string( $func ) ) {
            $function_array = preg_split("/@/", $func );
            if ( !isset($function_array[1]))
                throw FrameworkException::internalError('Routing Error');

            $class_name = 'App\\Controller\\' . $function_array[0];
            $method_name = $function_array[1];
            $class_name::$method_name();
        }
        return $this;
    }
}