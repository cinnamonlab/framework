<?php

namespace Framework\ErrorResponse;

use Framework\Exception\FrameworkException;
use Framework\Response;

class ErrorDisplayResponse extends ErrorResponse {

    function set( FrameworkException $e ) {
        $traces = $e->getTrace();
        $message = $e->getMessage();
        foreach( $traces as $t ) {
            $message .= "\n  in " . $t['file'] . " (line " . $t['line'] . ")"
                . "\n    at class " . $t['class'] . " with params " . print_r($t['args'], true) . "\n";
        }

        $this->setCode($e->getCode())
            ->setContent($message)
            ->setContentType('text/plain');
        return $this;
    }
}
