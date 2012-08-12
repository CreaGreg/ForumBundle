<?php

namespace Cornichon\ForumBundle\Exception;

class UserNotSetException extends \RuntimeException
{
    public function __construct($message = 'User is not set on your entity', \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}