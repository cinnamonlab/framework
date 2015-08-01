<?php

namespace Framework;

use Framework\Exception\FrameworkException;

class Response {

    /**
     * Create Response object with JSON header + content
     *
     * @param array $data
     * @param int $code HTTP code (optional)
     * @return Response object
     */

    static function json( $data, $code = 200 ) {
        return (new Response())
            ->setCode($code)
            ->setContent(json_encode($data))
            ->setContentType('application/json');
    }

    /**
     * Create Response object with Redirect options
     *
     * @param $redirect_to
     * @param int $code
     * @return $this
     */
    static function redirect( $redirect_to, $code = 302 ) {
        return (new Response())
            ->setCode($code)
            ->addHeader('Location: ' . $redirect_to);
    }

    /**
     * set Content for header
     *
     * @param $content
     * @return $this
     */
    function setContent( $content ) {
        $this->content = $content;
        return $this;
    }

    /**
     * Set HTTP code and put on header
     *
     * @param $code
     * @return $this
     */
    function setCode( $code ) {
        $this->code = $code;
        $this->addHeader($this->getDefaultHeaderFromCode( $code ));
        return $this;
    }

    /**
     * Get HTTP code;
     *
     * @return code
     */

    function getCode() {
        return $this->code;
    }

    /**
     * Get registered Header
     *
     * @return array of header information
     */
    function getHeaders() {
        return $this->headers;
    }

    /**
     * Get Content
     *
     * @return string content
     */
    function getContent() {
        return $this->content;
    }

    /**
     * Constructor
     */
    function __construct( ) {
        $this->headers = array();
        $this->code = 200;
        $this->content = "";
    }


    /**
     * Add Header
     *
     * @param $header
     * @return $this
     */
    function addHeader($header) {
        array_push( $this->headers, $header);
        return $this;
    }

    /**
     * Set Content Type
     * @param $content_type
     * @return $this
     */
    function setContentType( $content_type ) {
        $this->addHeader('Content-type: ' . $content_type);
        return $this;
    }

    /**
     * Set Display
     */

    function display( ) {
        $size = strlen($this->content);
        $this->addHeader('Content-length: ' . $size);

        foreach ( $this->headers as $header ) {
            header($header);
        }
        echo $this->content;
    }

    private $code;
    private $headers;
    private $content;

    private function getDefaultHeaderFromCode( $code ) {
        if ( $code == 200 ) return 'HTTP/1.0 200 OK';
        if ( $code == 301 ) return 'HTTP/1.0 301 Moved Permanently';
        if ( $code == 302 ) return 'HTTP/1.0 302 Found';
        if ( $code == 400 ) return 'HTTP/1.0 400 Bad Request';
        if ( $code == 401 ) return 'HTTP/1.0 401 Unauthorised';
        if ( $code == 403 ) return 'HTTP/1.0 403 Forbidden';
        if ( $code == 404 ) return 'HTTP/1.0 404 Not Found';
        if ( $code == 500 ) return 'HTTP/1.0 500 Internal Server Error';

        return 'HTTP/1.0 500 Internal Server Error';
    }

} 