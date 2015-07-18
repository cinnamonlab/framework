<?php

namespace Framework\ExceptionHandler;

use Framework\Exception\FrameworkException;
use Framework\Response;

class ErrorResponse extends Response {

    function set( FrameworkException $e ) {
        $this->setCode($e->getCode())
            ->setContent("")
            ->setContentType('text/plain');
        return $this;
    }
}