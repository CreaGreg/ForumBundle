<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\MessageStar;

use Cornichon\ForumBundle\Entity\TopicInterface;
use Cornichon\ForumBundle\Entity\MessageInterface;
use Cornichon\ForumBundle\Entity\MessageStarInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Doctrine\Common\Collections\ArrayCollection;

class MessageStarService extends BaseService
{

    protected function createMessageStar()
    {
        return new MessageStar();
    }

    /**
     * Save a starred message
     * 
     * @param    MessageStarInterface    $star
     * 
     * @return   MessageStarInterface
     */
    public function save(MessageStarInterface $star)
    {
        if ($star->getUser() === null) {
            throw new \RuntimeException('MessageStar entity must have a user associated to it.');
        }

        if ($star->getMessage() === null) {
            throw new \RuntimeException('MessageStar entity must have a message associated to it.');
        }

        $this->em->persist($star);
        $this->em->flush();

        return $star;
    }

    /**
     * Star a message
     * 
     * @param  MessageInterface   $message
     * @param  UserInterface      $user
     * 
     * @return boolen     whether the message starred or not
     */
    public function star(MessageInterface $message, UserInterface $user)
    {
        // whether the message was starred or not
        $changed = false;

        $star = $this->getByUserAndMessage($message, $user);

        if ($star === null) {
            $star = $this->createMessageStar();
            $star->setUser($user);
            $star->setMessage($message);
            $changed = true;
        }

        if ($star->isDeleted() === true) {
            $star->setIsDeleted(false);
            $changed = true;
        }

        if ($changed === true) {
            $message->increaseTotalStarred();
            $this->container->get('cornichon.forum.message')->save($message);

            $this->save($star);
        }

        return $changed;
    }

    /**
     * Unstar a message
     * 
     * @param  MessageInterface $message
     * @param  UserInterface    $user
     * 
     * @return boolen     whether the message unstarred or not
     */
    public function unstar(MessageInterface $message, UserInterface $user)
    {
        // whether the message was unstarred or not
        $changed = false;

        $star = $this->getByUserAndMessage($message, $user);

        if ($star !== null) {
            if ($star->isDeleted() === false) {
                $star->setIsDeleted(true);
                $changed = true;
            }

            if ($changed === true) {
                $message->decreaseTotalStarred();
                $this->container->get('cornichon.forum.message')->save($message);

                $this->save($star);
            }
        }

        return $changed;
    }

    /**
     * Return whether the message has been starred by the user or not
     * 
     * @param  MessageInterface $message
     * @param  UserInterface    $user
     * 
     * @return boolean
     */
    public function isMessageStarredByUser(MessageInterface $message, UserInterface $user)
    {
        $star = $this->getByUserAndMessage($message, $user);

        if ($star !== null && $star->isDeleted() === false) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get a MessageStar based on a user and a message
     * 
     * @param  MessageInterface   $message
     * @param  UserInterface      $user
     * 
     * @return MessageStarInterface
     */
    public function getByUserAndMessage(MessageInterface $message, UserInterface $user)
    {
        return $this->em
                    ->getRepository($this->messageStarRepositoryClass)
                    ->findOneBy(array('user' => $user, 'message' => $message));
    }

    /**
     * Returns an array of array keyed by the message id
     * 
     * @param  array  $messages   anything iterable
     * 
     * @return array
     */
    public function getStarsByMessages($messages)
    {
        $return = array();
        $tmpMessages = array();

        foreach ($messages as $m) {
            if ($m->getTotalStarred() !== 0) {
                $tmpMessages[] = $m;
            }
            $return[$m->getId()] = new ArrayCollection();
        }

        // we query the DB only if there are messages with stars
        if (count($tmpMessages) !== 0) {
            $stars = $this->em
                          ->getRepository($this->messageStarRepositoryClass)
                          ->findBy(array('message' => $tmpMessages));

            foreach ($stars as $star) {
                $return[$star->getMessage()->getId()]->add($star);
            }
        }

        return $return;
    }

}