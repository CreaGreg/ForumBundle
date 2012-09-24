<?php

namespace Cornichon\ForumBundle\Exception;

class InvalidBoardException extends \RuntimeException
{
    public function __construct($message = 'Board is invalid.', \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}