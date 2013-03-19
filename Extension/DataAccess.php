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
            "get_boards"              => new \Twig_Function_Method($this, 'getBoards'),
            "get_top_forum_users"     => new \Twig_Function_Method($this, 'getTopUsers'),
            "get_latest_moderations"  => new \Twig_Function_Method($this, 'getLatestModerations'),
            "get_latest_flags"        => new \Twig_Function_Method($this, 'getLatestFlags')
        );
    }

    public function getBoards($deleted = false, $parentFirst = false)
    {
        return $this->container->get('cornichon.forum.board')->getBoards($deleted, $parentFirst);
    }

    public function getTopUsers($limit = 10)
    {
        return $this->container->get('cornichon.forum.user_stat')->getTopUsers($limit);
    }

    public function getLatestModerations($limit = 15)
    {
        return $this->container->get('cornichon.forum.moderation')->getLatestModerations(0, $limit);
    }

    public function getLatestFlags($limit = 15)
    {
        return $this->container->get('cornichon.forum.flag')->getLatestFlags(0, $limit);
    }

    public function getName() 
    {
        return "Data_Access_Extension";
    }

}