<?php

namespace Framework\Exception;

use Exception;

/**
 * Class FrameworkException
 *
 * Exception class directly related to the Framework
 *
 * @package Framework\Exception
 */
class FrameworkException extends Exception {

    const ERROR_CODE_LACK_PARAMETER = 400;
    const ERROR_CODE_NOT_AUTHENTICATED = 401;
    const ERROR_CODE_FORBIDDEN = 403;
    const ERROR_CODE_NOT_FOUND = 404;
    const ERROR_CODE_INTERNAL = 500;
    const ERROR_CODE_MAINTENANCE = 503;

    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code, null);
    }

    /**
     * Get NotFound errors catchable
     *
     * @param $entity_name
     * @return FrameworkException
     */
    public static function notFound( $entity_name ) {
        return new FrameworkException("$entity_name not found",
            FrameworkException::ERROR_CODE_NOT_FOUND);
    }

    public static function lackParameter( $parameter_name ) {
        return new FrameworkException("Request lacks parameter $parameter_name",
            FrameworkException::ERROR_CODE_LACK_PARAMETER);
    }

    public static function internalError( $message ) {
        return new FrameworkException($message,
            FrameworkException::ERROR_CODE_INTERNAL);
    }
}