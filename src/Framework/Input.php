<?php

namespace Framework;

use Exception;
use Framework\Exception\FrameworkException;
use Framework\Validator\Validator;

class Input {

    private $parameters;
    private static $me;

    private static function getInstance( ) {
        if ( self::$me == null ) {
            self::$me = new Input();
        }
        return self::$me;
    }

    public static function get($key, $default = "", $validator = null)
    {
        $me = self::getInstance();

        if (isset($me->parameters[$key]) ) {
            $value = $me->parameters[$key];
        } else if ( isset($_REQUEST[$key]) ) {
            $value = $_REQUEST[$key];
        } else {
            $value = $me->processDefaultValue($default);
        }

        if ($validator instanceof Validator) {
            if ( $validator->execute($value) ) {
                return $value;
            } else {
                return $me->processDefaultValue($default);
            }

        } else if (is_callable($validator)) {
            try {
                if ($validator($value) ) {
                    return $value;
                } else {
                    return $me->processDefaultValue($default);
                }
            } catch (Exception $e) {
                throw FrameworkException::internalError("Validator error");
            }
        }
        return $value;

    }

    private function processDefaultValue( $default ) {
        if ( $default instanceof FrameworkException ) {
            throw $default;
        } else if ( is_callable( $default ) ) {
            return $default();
        }
        return $default;
    }

    static function set( $key, $value ) {
        $me = self::getInstance();
        $me->parameters[$key] = $value;
    }
}