<?php

namespace Framework\Validator;

/**
 * Class Validator
 */

abstract class Validator {

    /**
     * @param $parameter
     * @return boolean
     */
    abstract function execute( $parameter );
}