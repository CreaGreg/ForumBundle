<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Cornichon\ForumBundle\Entity\BoardInterface;
use Cornichon\ForumBundle\Entity\TopicInterface;

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
     * Get a Topic entity by a given topic slug
     * 
     * @param  string  $topicSlug
     * 
     * @return Topic
     */
    public function getBySlug($topicSlug)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->findOneBySlug($topicSlug);
    }

    /**
     * Get all topics in board
     * Performance can be poor because it will gather all data
     * 
     * @param  BoardInterface  $board
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getInBoard(BoardInterface $board)
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
     * @param  BoardInterface    $board
     * @param  integer           $offset
     * @param  integer           $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByBoard(BoardInterface $board, $offset, $limit)
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
     * @param  BoardInterface   $from
     * @param  BoardInterface   $to
     * @param  boolean          $flush = true
     * 
     * @return integer 
     */
    public function moveFromBoardToBoard(BoardInterface $from, BoardInterface $to, $flush = true)
    {
        return $this->em
                    ->getRepository($this->topicRepositoryClass)
                    ->moveFromBoardToBoard($from, $to);
    }

    public function flag(TopicInterface $topic, UserInterface $user)
    {

    }

    /**
     * Save a TopicInterface and check for data integrity
     * 
     * @param  TopicInterface  $topic
     * 
     * @return TopicInterface
     */
    public function save(TopicInterface $topic)
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
     * @param  TopicInterface    $topic
     * @param  boolean           $bubbleDown = false    if it should delete a related object
     * 
     * @return TopicInterface
     */
    public function delete(TopicInterface $topic, $bubbleDown = false)
    {
        $topic->setIsDeleted(true);

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

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->delete($topic, $user);

        return $topic;
    }

    /**
     * Undelete a topic
     * 
     * @param  TopicInterface   $topic
     * @param  boolean          $bubbleDown = false    if it should delete a related object
     * 
     * @return TopicInterface
     */
    public function undelete(TopicInterface $topic, $bubbleDown = false)
    {
        $topic->setIsDeleted(false);

        if ($bubbleDown === true) {
            $message = $this->container
                            ->get('cornichon.forum.message')
                            ->getTopicBodyByTopic($topic);

            if ($message !== null) {
                $this->container
                     ->get('cornichon.forum.message')
                     ->undelete($message, false);
            }
        }

        $this->em->persist($topic);
        $this->em->flush();

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->undelete($topic, $user);

        return $topic;
    }

    /**
     * Pin a topic
     * 
     * @param  TopicInterface    $topic
     * 
     * @return TopicInterface
     */
    public function pin(TopicInterface $topic)
    {
        $topic->setIsPinned(true);

        $this->save($topic);

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->unpin($topic, $user);

        return $topic;
    }

    /**
     * Unpin a topic
     * 
     * @param  TopicInterface    $topic
     * 
     * @return TopicInterface
     */
    public function unpin(TopicInterface $topic)
    {
        $topic->setIsPinned(false);

        $this->save($topic);

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->unpin($topic, $user);

        return $topic;
    }

    /**
     * Lock a topic
     * 
     * @param  TopicInterface    $topic
     * 
     * @return TopicInterface
     */
    public function lock(TopicInterface $topic)
    {
        $topic->setIsLocked(true);

        $this->save($topic);

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->lock($topic, $user);

        return $topic;
    }

    /**
     * Unlock a topic
     * 
     * @param  TopicInterface    $topic
     * 
     * @return TopicInterface
     */
    public function unlock(TopicInterface $topic)
    {
        $topic->setIsLocked(false);

        $this->save($topic);

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->unlock($topic, $user);

        return $topic;
    }

    /**
     * Add a given number to the views count of a given topic
     * 
     * @param  TopicInterface   $topic
     * @param  integer $increment = 1
     * 
     * @return TopicService
     */
    public function incrementTotalViews(TopicInterface $topic, $increment = 1)
    {
        $topic->increaseTotalPosts($increment);

        $this->save($topic);
    }

    /**
     * Add a given number to the posts count of a given topic
     * 
     * @param  TopicInterface   $topic
     * @param  integer $increment = 1
     * 
     * @return TopicService
     */
    public function incrementTotalPosts(TopicInterface $topic, $increment = 1)
    {
        $topic->increaseTotalPosts($increment);

        $this->save($topic);

        return $this;
    }
}