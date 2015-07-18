<?php

namespace Framework;

use Framework\Exception\FrameworkException;

class Response {

    private $code;
    private $headers;
    private $content;


    static function json( $data, $code = 200 ) {
        return (new Response())
            ->setCode($code)
            ->setContent(json_decode($data))
            ->setContentType('application/json');
    }

    static function handleException( FrameworkException $e, $type ) {
        if ( $type == 'json') {
            $data = array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            );
            return Response::json($data)->setCode($e->getCode());
        }

        if ( $type == 'debug') {
            $errors = $e->getTrace();
            //TODO: displaying Error Tracing without view logic

            $message = "<pre>" . $e->getMessage();
            foreach($errors as $error ) {

                $message .= "at function " . $error['function']
                    . "at line " . $error['line']
                    . "in file " . $error['file'] . "\n";
            }

            $message .= "</pre>";
            return (new Response())
                ->setContentType('text/html')
                ->setCode($e->getCode())
                ->setContent($message);
        }

        if ( $type == 'log' ) {
            //TODO: error log option
        }

        return (new Response())->setCode($e->getCode());

    }

    static function redirect( $redirect_to, $code = 302 ) {
        return (new Response())
            ->setCode($code)
            ->addHeader('Location: ' . $redirect_to);
    }

    function __construct( ) {
        $this->headers = array();
        $this->code = 200;
        $this->content = "";
    }

    function setContent( $content ) {
        $this->content = $content;
        return $this;
    }

    function setCode( $code ) {
        $this->code = $code;
        $this->addHeader($this->getDefaultHeaderFromCode( $code ));
        return $this;
    }


    function addHeader($header) {
        array_push( $this->headers, $header);
        return $this;
    }

    function setContentType( $content_type ) {
        $this->addHeader('Content-type: ' . $content_type);
        return $this;
    }

    function display( ) {
        $size = strlen($this->content);
        $this->addHeader('Content-length: ' . $size);

        foreach ( $this->headers as $header ) {
            header($header);
        }
        echo $this->content;
    }

    private function getDefaultHeaderFromCode( $code ) {
        if ( $code == 200 ) return 'HTTP/1.0 200 OK';
        if ( $code == 301 ) return 'HTTP/1.0 301 Moved Permanently';
        if ( $code == 302 ) return 'HTTP/1.0 302 Found';
        if ( $code == 400 ) return 'HTTP/1.0 400 Bad Request';
        if ( $code == 401 ) return 'HTTP/1.0 400 Unauthorised';
        if ( $code == 403 ) return 'HTTP/1.0 403 Forbidden';
        if ( $code == 404 ) return 'HTTP/1.0 404 Not Found';
        if ( $code == 500 ) return 'HTTP/1.0 500 Internal Server Error';

        return 'HTTP/1.0 500 Internal Server Error';
    }

} 