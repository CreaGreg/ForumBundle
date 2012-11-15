<?php

namespace Cornichon\ForumBundle\Exception;

class TopicExistsException extends \RuntimeException
{
    public function __construct($message = 'Topic exists', \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}