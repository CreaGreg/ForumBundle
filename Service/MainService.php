<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Doctrine\Common\Collections\ArrayCollection;

class MainService extends BaseService
{

    /**
     * Build a URL based on a given entity and a route
     * 
     * @param  string  $routeName
     * @param  object  $entity
     * 
     * @return string
     */
    public function forumPath($routeName, $entity = null)
    {
        $parameters = array();
        $board = null;
        $topic = null;
        if ($entity instanceof Board) {
            $board = $entity;
        }
        if ($entity instanceof Topic) {
            $board = $entity->getBoard();
            $topic = $entity;
        }
        if ($entity instanceof Message) {
            $board = $entity->getTopic()->getBoard();
            $topic = $entity->getTopic();
            $parameters['messageId'] = $entity->getId();
        }

        if ($topic instanceof Topic) {
            $parameters['topicSlug'] = $topic->getSlug();
            $parameters['topicId'] = $topic->getId();
        }
        if ($board instanceof Board) {
            $boardSlug = $this->container->get('cornichon.forum.board')->buildSlug($board);

            $parameters['boardSlug'] = $boardSlug;
            $parameters['boardId'] = $board->getId();
        }

        return $this->container->get('router')->generate($routeName, $parameters);
    }

}