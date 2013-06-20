<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\Flag;
use Cornichon\ForumBundle\Entity\Moderation;

use Cornichon\ForumBundle\Entity\MessageInterface;
use Cornichon\ForumBundle\Entity\TopicInterface;
use Cornichon\ForumBundle\Entity\FlagInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

class FlagService extends BaseService
{

    protected function createFlag()
    {
        return new Flag();
    }


    /**
     * Flag a topic or a message
     * 
     * @param  Topic|Message   $entity
     * @param  UserInterface   $user
     * 
     * @return boolean
     */
    public function flag($entity, UserInterface $user)
    {
        // we only accept a Topic entity or a Message entity
        if (false === $entity instanceof Topic && false === $entity instanceof Message) {
            throw new \InvalidArgumentException('Entity should be either a Topic entity or a Message entity.');
        }

        if ($entity->isDeleted() === true) {
            return false; // we don't bother flagging an item it already has been deleted
        }

        if (true === $entity instanceof Message) {
            $flag = $this->getByMessage($entity);
        }
        else if (true === $entity instanceof Topic) {
            $flag = $this->getByTopic($entity);    
        }
        
        // if a flag for this entity doesn't exists, we initialize a new one
        if ($flag === null) {
            $flag = $this->createFlag();
            $flag->setDateCreated(new \DateTime());

            if (true === $entity instanceof Message) {
                $flag->setMessage($entity);
            }
            if (true === $entity instanceof Topic) {
                $flag->setTopic($entity);
            }
        }
        // otherwise we check if the user already flagged this entity
        else {
            foreach ($flag->getUsers() as $u) {
                if ($u->getId() === $user->getId()) {
                    return true; // if the user already flagged the entity we silently validate anyways
                }
            }
        }

        $flag->addUser($user);
        $flag->setTotalFlagged($flag->getUsers()->count());

        $this->save($flag);

        return true;
    }

    /**
     * Save a flag
     * 
     * @param  FlagInterface   $flag
     * 
     * @return FlagInterface
     */
    public function save(FlagInterface $flag)
    {
        $this->em->persist($flag);
        $this->em->flush();

        return $flag;
    }

    /**
     * Get a flag by topic
     * 
     * @param  TopicInterface  $topic
     * 
     * @return FlagInterface|null
     */
    public function getByTopic(TopicInterface $topic)
    {
        return $this->em
                    ->getRepository($this->flagRepositoryClass)
                    ->findOneByTopic($topic);
    }

    /**
     * Get a flag by message
     * 
     * @param  MessageInterface  $message
     * 
     * @return FlagInterface|null
     */
    public function getByMessage(MessageInterface $message)
    {
        return $this->em
                    ->getRepository($this->flagRepositoryClass)
                    ->findOneByMessage($message);
    }

    /**
     * Get a collection of the latest flag
     * 
     * @param  integer   $offset = 0
     * @param  integer   $limit = 15
     * 
     * @return Paginator
     */
    public function getLatestFlags($offset = 0, $limit = 15)
    {
        return $this->em
                    ->getRepository($this->flagRepositoryClass)
                    ->getLatestFlags($offset, $limit);
    }

}