<?php

namespace Framework\ErrorResponse;

use Framework\Exception\FrameworkException;
use Framework\Response;

class ErrorDisplayResponse extends ErrorResponse {

    function set( FrameworkException $e ) {
        $this->setCode($e->getCode())
            ->setContent(print_r($e->getTrace()))
            ->setContentType('text/plain');
        return $this;
    }
}
