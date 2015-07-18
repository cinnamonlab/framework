<?php

namespace Framework\Validator;

/**
 * Class IsNumericValidator
 *
 * @package Framework\Validator
 */
class IsNumericValidator extends Validator {

    /**
     * return true if $parameter is numeric
     * @param $parameter
     * @return bool
     */

    function execute( $parameter ) {
        if ( is_numeric($parameter) ) return true;
        return false;
    }

}