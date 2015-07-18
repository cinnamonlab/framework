<?php
namespace Framework;

use Framework\Exception\FrameworkException;
use Framework\ExceptionHandler\ErrorResponse;
use Framework\Validator\Validator;
use Exception;

class Router
{
    private static $me;

    private $parameters;
    private $path;
    private $is_called;
    private $error_response;

    private function __construct( ) {
        $this->is_called = false;
        $this->error_response = null;
        $request_uri = preg_split( "/\?/", $_SERVER['REQUEST_URI'], 2 );
        if ( isset($request_uri[1]) ) {
            parse_str($request_uri, $this->parameters);
        } else {
            $this->parameters = array();
        }
        $requestUri = preg_split( "/\//", $request_uri[0] );

        $scriptName = preg_split( "/\//", $_SERVER['SCRIPT_NAME'] );


        foreach ($scriptName as $key => $value) {
            if ($value == $requestUri[$key]){
                unset($requestUri[$key]) ;
            }
        }
        $this->path = array_values($requestUri);

    }

    private static function getInstance( ) {
        if ( self::$me == null ) {
            self::$me = new Router();
        }
        return self::$me;
    }

    private function processDefaultValue( $default ) {
        if ( $default instanceof FrameworkException ) {
            throw $default;
        } else if ( is_callable( $default ) ) {
            return $default();
        }
        return $default;
    }

    /**
     * @param $method
     * @param $path
     * @param $function
     * @return bool
     */

    static function action($method, $path, $function) {

        if ( $method != $_SERVER['REQUEST_METHOD'] )
            return false;

        $me = self::getInstance();
        if ( $me->is_called == true ) return false;

        $path_array = preg_split("/\//", $path);
        foreach( $path_array as $key => $path_element ) {
            if ( strlen(trim($path_element)) == 0 ) {
                unset($path_array[$key]);
            }
        }
        $path_array = array_values($path_array);

        foreach( $path_array as $key => $path_element ) {
            if ( preg_match("/^\:(.*)$/", $path_element, $match)
                || preg_match("/^\{(.*)\}$/", $path_element, $match )) {
                $me->parameters[$match[1]] = $me->path[$key];
            } else {
                if ( $me->path[$key] != $path_element ) {
                    return false;
                }
            }
        }

        try {
            if ( is_callable( $function ) ) {
                $response = $function();
            } else {

                $function_array = preg_split("/@/", $function );
                if ( !isset($function_array[1]))
                    throw FrameworkException::internalError('Routing Error');

                $class_name = $function_array[0];
                $method_name = $function_array[1];

                $response = $class_name::$method_name();
            }

            if ( $response instanceof Response ) {
                $response->display();
            } else {
                $rs = new Response();
                $rs->setContentType('text/html')
                    ->setContent($response)
                    ->display();
            }
            $me->is_called = true;
        } catch ( FrameworkException $e ) {
            $me->handleError($e);

        } catch ( Exception $e ) {
            $exception = FrameworkException::internalError('Internal Error');
            $me->handleError($exception);
        }
        return true;
    }


    private function handleError( FrameworkException $e ) {
        if ( $this->error_response == null ) {
            $this->error_response = new ErrorResponse();
        }
        $this->error_response->set( $e )->display();
        return true;
    }

    static function set( $key, $value ) {
        $me = self::getInstance();
        $me->parameters[$key] = $value;
    }

    static function get( $key, $default = "", $validator = null ) {
        $me = self::getInstance();

        if ( isset($me->parameters[$key]) ) {

            $value = $me->parameters[$key];

            if ( $validator instanceof Validator ) {
                if ( $validator->execute($value) )
                    return $value;

            } else if ( is_callable( $validator ) ) {
                try {
                    if ( $validator( $value ) )
                        return $value;

                } catch (Exception $e) {
                    throw FrameworkException::internalError("Validator error");
                }
            }
            return $value;
        }

        return $me->processDefaultValue($default);

    }

}