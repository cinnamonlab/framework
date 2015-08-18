<?php

namespace Framework\ErrorResponse;

use Framework\Exception\FrameworkException;

class ErrorDisplayResponse extends ErrorResponse {

    function set( FrameworkException $e ) {
        $message = $e->getTraceAsString();
        $this->setCode($e->getCode())
            ->setContent($message)
            ->setContentType('text/plain');
        return $this;
    }
}
