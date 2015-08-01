<?php

namespace Framework;

use Exception;
use Framework\Exception\FrameworkException;
use Framework\Validator\Validator;

class Input {

    /**
     * Get Value
     *
     * @param $key
     * @param string $default
     * @param null $validator
     * @return mixed
     * @throws Exception\FrameworkException
     */

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

    static function has($key) {
        $me = self::getInstance();
        if ( isset($me->parameters[$key])) return true;
        return false;
    }

    /**
     * Get Uploaded File
     *
     * @param $key
     * @param null $default
     * @param null $validator
     * @return InputFile
     */

    static function file( $key, $default = null, $validator = null) {
        if ( ! isset($_FILES[$key])
            || $_FILES[$key]['error'] != UPLOAD_ERR_OK)
            return self::processError($default);

        $file = new InputFile($_FILES['key']['tmp_name']);
        $file->setContentType($_FILES['key']['type']);
        $file->setOriginalName($_FILES['key']['name']);

        if ($validator instanceof Validator) {
            if ( $validator->execute( $file ) ) {
                return $file;
            } else {
                return self::processError($default);
            }

        } else if (is_callable($validator)) {
            try {
                if ($validator($file) ) {
                    return $file;
                } else {
                    return self::processError($default);
                }
            } catch (Exception $e) {
                throw FrameworkException::internalError("Validator error");
            }
        }

        return $file;
    }
    /**
     * Set Value Manually
     *
     * @param $key
     * @param $value
     */
    static function set( $key, $value ) {
        $me = self::getInstance();
        $me->parameters[$key] = $value;
    }

    private function processDefaultValue( $default ) {
        if ( $default instanceof FrameworkException ) {
            throw $default;
        } else if ( is_callable( $default ) ) {
            return $default();
        }
        return $default;
    }

    private $parameters;
    private static $me;

    private static function getInstance( ) {
        if ( self::$me == null ) {
            self::$me = new Input();
        }
        return self::$me;
    }

    private static function processError($throw_exception) {
        if ( $throw_exception instanceof Exception )
            throw $throw_exception;
        return $throw_exception;
    }
}