<?php

namespace Cornichon\ForumBundle\Extension;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Path extends \Twig_Extension {

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions() 
    {
        return array(
            "forum_path" => new \Twig_Function_Method($this, 'forumPath')
        );
    }

    public function forumPath($routeName, $entity = null)
    {
        return $this->container->get('cornichon.forum')->forumPath($routeName, $entity);
    }

    public function getName() 
    {
        return "Path_Extension";
    }

}