<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

class TopicService extends BaseService
{

    protected function createTopicStat()
    {
        return new TopicStat();
    }
    
    protected function createTopic()
    {
        return new Topic();
    }

    /**
     * Get a Topic entity by a given topic id
     * 
     * @param  integer  $topicId
     * 
     * @return Topic
     */
    public function getById($topicId)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->find($topicId);
    }

    /**
     * Get all topics in board
     * Performance can be poor because it will gather all data
     * 
     * @param  Board  $board
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getInBoard(Board $board)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->getInBoard($board);
    }

    /**
     * Get the latest topics in general
     * 
     * @param  integer  $offset
     * @param  itenger  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopics($offset, $limit)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->getLatestTopics($offset, $limit);
    }

    /**
     * Get the latest topics by board ids
     * 
     * @param  array    $boardIds
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByBoardIds($boardIds, $offset, $limit)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->getLatestTopicsByBoardIds($boardIds, $offset, $limit);
    }

    /**
     * Get the latest topics by board
     * 
     * @param  Board    $board
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByBoard(Board $board, $offset, $limit)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->getLatestTopicsByBoard($board, $offset, $limit);
    }

    /**
     * Get the latest topics by user
     * 
     * @param  UserInterface    $user
     * @param  integer          $offset
     * @param  integer          $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByUser(UserInterface $user, $offset, $limit)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->getLatestTopicsByUser($user, $offset, $limit);
    }


    /**
     * Get all topics
     * Do not use if you don't know what you are doing
     * 
     * @return ArrayCollection
     */
    public function getAll()
    {
        return new ArrayCollection($this->em->getRepository($this->topicRepositoryClass)->findAll());
    }

    /**
     * Move all topics from a board to another board
     * 
     * @param  Board   $from
     * @param  Board   $to
     * @param  boolean $flush = true
     * 
     * @return integer 
     */
    public function moveFromBoardToBoard(Board $from, Board $to, $flush = true)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->moveFromBoardToBoard($from, $to);
    }

    public function flag(Topic $topic, UserInterface $user)
    {

    }

    /**
     * Save a Topic and check for data integrity
     * 
     * @param  Topic  $topic
     * 
     * @return Topic
     */
    public function save(Topic $topic)
    {
        // If no user is specified, pick the current one
        if ($topic->getUser() === null) {
            $topic->setUser($this->container->get('security.context')->getToken()->getUser());
        }

        // Generate the topic slug
        if ($topic->getSlug() === null) {
            $topic->setSlug();
        }

        // If no board has been selected, throw an exception
        if ($topic->getBoard() === null) {
            throw new \Cornichon\ForumBundle\Exception\BoardNotSetException();
        }

        // If the topic doesn't have stat
        if ($topic->getLastUser() === null) {
            $topic->setLastUser($topic->getUser());
        }

        // If the topic is new
        if ($topic->getId() === null) {
            $topic->setDateCreated(new \DateTime());

            // Get the user stat
            $userStat = $this->container
                             ->get('cornichon.forum.user_stat')
                             ->getByUserOrCreateOne($topic->getUser(), false);

            $userStat->increaseTotalTopic();
            $userStat->increaseTotalMessage();
        }
        else {
            $topic->getDateModified(new \DateTime());
        }

        $this->em->persist($topic);
        $this->em->flush($topic);

        return $topic;
    }

    /**
     * Delete a topic
     * 
     * @param  Topic    $topic
     * @param  boolean  $bubbleDown = false    if it should delete a related object
     * 
     * @return Topic
     */
    public function delete(Topic $topic, $bubbleDown = false)
    {
        $topic->setIsDeleted(!$topic->isDeleted());

        if ($bubbleDown === true) {
            $message = $this->container
                            ->get('cornichon.forum.message')
                            ->getTopicBodyByTopic($topic);

            if ($message !== null) {
                $this->container
                     ->get('cornichon.forum.message')
                     ->delete($message, false);
            }
        }

        $this->em->persist($topic);
        $this->em->flush();

        return $topic;
    }

    /**
     * Add a given number to the views count of a given topic
     * 
     * @param  Topic   $topic
     * @param  integer $increment = 1
     * 
     * @return TopicService
     */
    public function incrementTotalViews(Topic $topic, $increment = 1)
    {
        $topic->increaseTotalPosts($increment);

        $this->save($topic);
    }

    /**
     * Add a given number to the posts count of a given topic
     * 
     * @param  Topic   $topic
     * @param  integer $increment = 1
     * 
     * @return TopicService
     */
    public function incrementTotalPosts(Topic $topic, $increment = 1)
    {
        $topic->increaseTotalPosts($increment);

        $this->save($topic);

        return $this;
    }
}