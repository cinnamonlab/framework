<?php

namespace Framework\Validator;

/**
 * Class NotBlankValidator
 * @package Framework\Validator
 */
class NotBlankValidator extends Validator {

    /**
     * return true if $parameter is not blank
     * @param $parameter
     * @return bool
     */

    function execute( $parameter ) {
        if ( strlen($parameter) == 0 ) return false;
        return true;
    }

}