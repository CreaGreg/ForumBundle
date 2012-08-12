<?php

namespace Cornichon\ForumBundle\Exception;

class TopicNotSetException extends \RuntimeException
{
    public function __construct($message = 'Topic is not set on your entity', \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}