<?php

namespace Framework\Validator;

/**
 * Class IsInArrayValidator
 * @package Framework\Validator
 */
class IsInArrayValidator extends Validator {

    private $options;

    function __construct( $array ) {
        if ( is_array( $array ) ) {
            $this->options = $array;
        } else {
            $this->options = array();
        }
    }

    /**
     * return true if $parameter is numeric
     * @param $parameter
     * @return bool
     */

    function execute( $parameter ) {
        if ( in_array($parameter, $this->options) ) return true;
        return false;
    }

}