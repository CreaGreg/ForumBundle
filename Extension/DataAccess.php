<?php

namespace Cornichon\ForumBundle\Extension;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DataAccess extends \Twig_Extension {

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions() 
    {
        return array(
            "get_boards" => new \Twig_Function_Method($this, 'getBoards')
        );
    }

    public function getBoards()
    {
        return $this->container->get('cornichon.forum.board')->getBoards();
    }

    public function getName() 
    {
        return "Data_Access_Extension";
    }

}