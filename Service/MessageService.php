<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Cornichon\ForumBundle\Entity\TopicInterface;
use Cornichon\ForumBundle\Entity\MessageInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Tools\Pagination\Paginator;

class MessageService extends BaseService
{

    protected function createMessage()
    {
        return new Message();
    }

    /**
     * Get a message by its id
     * 
     * @param  integer   $id
     * @param  boolean   $isDeleted
     * 
     * @return MessageInterface|null
     */
    public function find($id, $isDeleted)
    {
        return $this->em
                    ->getRepository($this->messageRepositoryClass)
                    ->find($id, $isDeleted);
    }

    /**
     * Get a collection of messages by topic
     * 
     * @param  TopicInterface   $topic
     * @param  integer          $offset
     * @param  integer          $limit
     * @param  boolean          $isDeleted = false
     * 
     * @return Paginator
     */
    public function getMessagesByTopic(TopicInterface $topic, $offset, $limit, $isDeleted = false)
    {
        return $this->em
                    ->getRepository($this->messageRepositoryClass)
                    ->getMessagesByTopic($topic, $offset, $limit, $isDeleted);
    }

    /**
     * Get a topic body by topic
     * 
     * @param  TopicInterface  $topic
     * 
     * @return MessageInterface|null
     */
    public function getTopicBodyByTopic(TopicInterface $topic)
    {
        return $this->em
                    ->getRepository($this->messageRepositoryClass)
                    ->getTopicBodyByTopic($topic);
    }

    /**
     * Flag a message object
     * 
     * @param  MessageInterface   $message
     * @param  UserInterface      $user
     * 
     * @return MessageInterface
     */
    public function flag(MessageInterface $message, UserInterface $user)
    {

        return $message;
    }

    /**
     * Save a Message object
     * 
     * @param  MessageInterface   $message
     * 
     * @return MessageInterface
     */
    public function save(MessageInterface $message)
    {
        if ($message->getUser() === null) {
            $message->setUser($this->container->get('security.context')->getToken()->getUser());
        }

        if ($message->getTopic() === null) {
            throw new \Cornichon\ForumBundle\Exception\TopicNotSetException();
        }

        /**
         * Update the last message of topic
         */
        $message->getTopic()->setLastMessageDate(new \DateTime());

        if ($message->getId() === null) {
            $message->setDateCreated(new \DateTime());

            if ($message->isTopicBody() === false) {
                /**
                 * Increment board message count when creating a new message
                 */
                $this->container
                     ->get('cornichon.forum.board')
                     ->incrementStatPosts($message->getTopic()->getBoard());

                $this->container
                     ->get('cornichon.forum.topic')
                     ->incrementTotalPosts($message->getTopic());

                // Get the user stat
                $userStat = $this->container
                                 ->get('cornichon.forum.user_stat')
                                 ->getByUserOrCreateOne($message->getUser());

                $userStat->increaseTotalMessage();

                $this->em->persist($userStat);
            }
            else {
                /**
                 * Increment board topic count when creating a new topic
                 */
                $this->container
                     ->get('cornichon.forum.board')
                     ->incrementStatTopics($message->getTopic()->getBoard());
            }

            // should start by 1 (for new topic)
            $message->setPosition($message->getTopic()->getTotalPosts());
        }
        else {
            $message->getDateModified(new \DateTime());
        }

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    /**
     * Delete a message
     * 
     * @param  MessageInterface  $message
     * @param  boolean           $bubbleUp  = false     if it should delete a related object as well
     * 
     * @return MessageInterface
     */
    public function delete(MessageInterface $message, $bubbleUp = false)
    {
        if ($message->isTopicBody() === true && $bubbleUp === true) {
            $this->container
                 ->get('cornichon.forum.topic')
                 ->delete($message->getTopic());
        }

        $message->setIsDeleted(true);

        $this->em->persist($message);
        $this->em->flush();

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->delete($message, $user);

        return $message;
    }

    /**
     * Undelete a message
     * 
     * @param  MessageInterface    $message
     * @param  boolean             $bubbleUp  = false     if it should delete a related object as well
     * 
     * @return MessageInterface
     */
    public function undelete(MessageInterface $message, $bubbleUp = false)
    {
        if ($message->isTopicBody() === true && $bubbleUp === true) {
            $this->container
                 ->get('cornichon.forum.topic')
                 ->undelete($message->getTopic());
        }

        $message->setIsDeleted(false);

        $this->em->persist($message);
        $this->em->flush();

        $user = $this->container
                     ->get('security.context')
                     ->getToken()
                     ->getUser();

        $this->container
             ->get('cornichon.forum.moderation')
             ->undelete($message, $user);

        return $message;
    }
}