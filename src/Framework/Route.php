<?php
namespace Framework;

use Framework\ErrorResponse\ErrorDisplayResponse;
use Framework\Exception\FrameworkException;
use Framework\ErrorResponse\ErrorResponse;
use Exception;
use Framework\Processor\IgnoreProcessor;
use Framework\Processor\PostProcessor;
use Framework\Processor\Processor;

/**
 * Class Route
 *
 * @package Framework
 */
class Route
{

    static function otherwise( $function ) {
        self::action('otherwise', null, $function);
    }

    static function action($method, $path, $function) {
        $restInput =array();
        $me = self::getInstance();

        if ( Input::has('__request_method') ) $request_method = Input::get('__request_method');
        else $request_method = $_SERVER['REQUEST_METHOD'];
        if ($me->is_called == true) return IgnoreProcessor::getInstance();

        if ( $method != 'otherwise' ) {

            if ($method != $request_method )
                return IgnoreProcessor::getInstance();


            $path_array = preg_split("/\//", $path);
            foreach ($path_array as $key => $path_element) {
                if (strlen(trim($path_element)) == 0) {
                    unset($path_array[$key]);
                }
            }
            $path_array = array_values($path_array);

            if(count($path_array)!=count($me->path)) {
                return $me->getPostProcessor();
            }

            foreach ($path_array as $key => $path_element) {
                if (preg_match("/^\:(.*)$/", $path_element, $match)
                    || preg_match("/^\{(.*)\}$/", $path_element, $match)
                ) {
                    Input::set($match[1], $me->path[$key]);
                    $restInput[]=$me->path[$key];
                } else {
                    if (!isset($me->path[$key]) ||
                        $me->path[$key] != $path_element
                    ) {
                        return IgnoreProcessor::getInstance();
                    }
                }
            }
        }

        if ( $me->skip ) return $me->getPostProcessor();

        try {
            if ( is_callable( $function ) ) {
                $response = $function();
            } else {

                $function_array = preg_split("/@/", $function );
                if ( !isset($function_array[1]))
                    throw FrameworkException::internalError('Routing Error');

                $class_name = 'App\\Controller\\' . $function_array[0];
                $method_name = $function_array[1];

                //$response = $class_name::$method_name();
                // Initialization controller object
                $controller = new $class_name;

                $response=call_user_func_array(array($controller,$method_name),$restInput);
                //$response = $controller->$method_name();
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
            $me->handleError( $e );
            $me->is_called = true;
            return IgnoreProcessor::getInstance();

        } catch ( Exception $e ) {
            $exception = FrameworkException::internalError('Internal Error: ' . $e->getMessage( ) );
            $me->handleError($exception);
            $me->is_called = true;
            return IgnoreProcessor::getInstance();
        }
        return $me->getPostProcessor();
    }

    static function get($path, $function) {
        return self::action('GET', $path, $function);
    }
    static function post($path, $function) {
        return self::action('POST', $path, $function);
    }
    static function put($path, $function) {
        return self::action('PUT', $path, $function);
    }
    static function patch($path, $function) {
        return self::action('PATCH', $path, $function);
    }
    static function delete($path, $function) {
        return self::action('DELETE', $path, $function);
    }

    public static function reset( ) {
        self::$me = null;
    }

    public static function setErrorResponse( ErrorResponse $e ) {
        $me = self::getInstance();
        $me->error_response = $e;
    }

    public static function setPostProcessor( Processor $p ) {
        $me = self::getInstance();
        $me->post_processor = $p;
    }


    private $skip = false;

    public static function setSkipMain( ) {
        self::getInstance()->skip = true;
    }


    private function getPostProcessor( ) {
        if ( $this->post_processor ) return $this->post_processor;

        if ( Config::has('app.post_processor') ) {
            $this->post_processor = Config::get('app.post_processor');
        } else {
            $this->post_processor = new PostProcessor();

        }
        return $this->post_processor;
    }


    private static $me;
    private $path;
    private $is_called;
    private $error_response;
    private $post_processor;

    private function __construct( ) {
        $this->is_called = false;
        $this->error_response = null;
        $this->post_processor = null;

        if ( Input::has('__request_uri') ) {
            $this->path = Input::get('__request_uri');
        } else {
            $request_uri = preg_split( "/\?/", $_SERVER['REQUEST_URI'], 2 );
            $requestUri = preg_split( "/\//", $request_uri[0] );
            $scriptName = preg_split( "/\//", $_SERVER['SCRIPT_NAME'] );

            foreach ($scriptName as $key => $value) {
                if ($value == $requestUri[$key]){
                    unset($requestUri[$key]) ;
                }
            }

            foreach($requestUri as $key => $path_element) {
                if (strlen(trim($path_element)) == 0) {
                    unset($requestUri[$key]);
                }
            }

            $this->path = array_values($requestUri);

            Input::set('__request_uri', $this->path);
            Input::set('__request_method', $_SERVER['REQUEST_METHOD']);
        }


    }

    private static function getInstance( ) {
//        if ( self::$me == null ) {
//            self::$me = new Route();
//        }
//        return self::$me;
//        is this acceptable?
        return self::$me?: self::$me = new Route();
    }

    private function handleError( FrameworkException $e ) {
        if ( $this->error_response == null ) {
//            if ( Config::has('app.error_response') )
//                $this->error_response = Config::get('app.error_response');
//            else if ( Config::get('app.debug', true ) )
//                $this->error_response = new ErrorDisplayResponse();
//            else $this->error_response = new ErrorResponse();
            $this->error_response = Config::get('app.error_response') ?:
                (Config::get('app.debug', true ) ? new ErrorDisplayResponse() : new ErrorResponse());
        }
        $this->error_response->set( $e )->display();
        return true;
    }


}