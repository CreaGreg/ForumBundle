<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Doctrine\Common\Collections\ArrayCollection;

class MainService extends BaseService {

    public function forumPath($routeName, $entity = null)
    {
        $parameters = array();
        $board = null;
        if ($entity instanceof Board) {
            $board = $entity;
        }
        if ($entity instanceof Topic) {
            $board = $entity->getBoard();
            $parameters['topicSlug'] = $entity->getSlug();
            $parameters['topicId'] = $entity->getId();
        }
        if ($entity instanceof Message) {
            $board = $entity->getTopic()->getBoard();
        }

        if ($board instanceof Board) {
            $boardSlug = $this->container->get('cornichon.forum.board')->buildSlug($board);

            $parameters['boardSlug'] = $boardSlug;
            $parameters['boardId'] = $board->getId();
        }

        return $this->container->get('router')->generate($routeName, $parameters);
    }

}