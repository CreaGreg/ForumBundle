<?php

namespace Cornichon\ForumBundle\Exception;

class BoardExistsException extends \RuntimeException
{
    public function __construct($message = 'Board exists', \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}