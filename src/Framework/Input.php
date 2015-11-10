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

//        if (isset($me->parameters[$key]) ) {
//            $value = $me->parameters[$key];
//        } else if ( isset($_REQUEST[$key]) ) {
//            $value = $_REQUEST[$key];
//        } else {
//            $value = $me->processDefaultValue($default);
//        }
        // can be write like this
        $value = $me->parameters[$key] ?: ($_REQUEST[$key] ?: $me->processDefaultValue($default));

//        refactor code
//        if ($validator instanceof Validator) {
//            if ( $validator->execute($value) ) {
//                return $value;
//            } else {
//                return $me->processDefaultValue($default);
//            }
//        } else if (is_callable($validator)) {
//            try {
//                if ($validator($value) ) {
//                    return $value;
//                } else {
//                    return $me->processDefaultValue($default);
//                }
//            } catch (Exception $e) {
//                throw FrameworkException::internalError("Validator error");
//            }
//        }
//        return $value;
        return self::validate($value, $validator, $default);
    }

     function parseInput( ) {
        parse_str(file_get_contents('php://input'), $parameters);
        return $parameters;
    }

    static function has($key) {
        $me = self::getInstance();
        if ( isset($me->parameters[$key])) return true;

        if ( isset($_REQUEST[$key]) ) return true;
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

        $file = new InputFile($_FILES[$key]['tmp_name']);
        $file->setContentType($_FILES[$key]['type']);
        $file->setOriginalName($_FILES[$key]['name']);
//      could decompose to one function
//        if ($validator instanceof Validator) {
//            if ( $validator->execute( $file ) ) {
//                return $file;
//            } else {
//                return self::processError($default);
//            }
//
//        } else if (is_callable($validator)) {
//            try {
//                if ($validator($file) ) {
//                    return $file;
//                } else {
//                    return self::processError($default);
//                }
//            } catch (Exception $e) {
//                throw FrameworkException::internalError("Validator error");
//            }
//        }
//        return $file;
        return self::validate($file, $validator, $default);
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

    private $file_serialization = false;

    public static function file_serialize( $value = true) {
        self::getInstance()->file_serialization = $value;
    }

    public static function getAllData( ) {
        $response = array('parameters' => $_REQUEST );

        $me = self::getInstance();

        foreach( $me->parameters as $key => $value ) {
            $response['parameters'][$key] = $value;
        }


        if ( $me->file_serialization ) {
            if (isset($_FILES) && count($_FILES) > 0) {
                foreach ($_FILES as $key => $file) {
                    if ($file['error'] == UPLOAD_ERR_OK) {
                        do {
                            $to_file = __APP__ . "/storage/files/" . md5($file['tmp_name'] . rand(0, 100000));
                        } while (file_exists($to_file));

                        if (copy($file['tmp_name'], $to_file)) {
                            $response['files'][$key] = $file;
                            $response['files']['tmp_name'] = $to_file;
                        }

                    }
                }
            }
        }
        return $response;
    }

    public static function bind( $data ) {
        $me = self::getInstance();
        if ( isset($data['parameters'] ) ) {
            $me->parameters = $data['parameters'];
        }

        if ( isset($data['files'] ) ) {
            $_FILES = $data['files'];
        }
        return;
    }

    private function __construct( ) {
        if ( isset($_SERVER['REQUEST_METHOD']) &&
            $_SERVER['REQUEST_METHOD'] != 'POST' &&
            $_SERVER['REQUEST_METHOD'] != 'GET') {
            $parameters = $this->parseInput();
            foreach($parameters as $key=>$v) {
                $this->parameters[$key] = $v;
            }
        }
    }

    private static function validate($data, $validator, $default)
    {
        if ($validator instanceof Validator) {
            if ( $validator->execute( $data ) ) {
                return $data;
            } else {
                return self::processError($default);
            }

        } else if (is_callable($validator)) {
            try {
                if ($validator($data) ) {
                    return $data;
                } else {
                    return self::processError($default);
                }
            } catch (Exception $e) {
                throw FrameworkException::internalError("Validator error");
            }
        }
        return $data;
    }
}