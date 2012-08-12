<?php

namespace Cornichon\ForumBundle\Exception;

class BoardNotSetException extends \RuntimeException
{
    public function __construct($message = 'Board is not set on your entity', \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}