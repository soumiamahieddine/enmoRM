<?php

namespace core\Exception;
/**
 * Exception for conflicted request
 */
class ConflictException
    extends \core\Exception
{
    /**
     * Constructor
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($message="Conflict", $code=409, $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}