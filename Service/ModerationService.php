<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\Flag;
use Cornichon\ForumBundle\Entity\Moderation;

use Cornichon\ForumBundle\Entity\TopicInterface;
use Cornichon\ForumBundle\Entity\ModerationInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;

use \Symfony\Component\Security\Core\User\UserInterface;

class ModerationService extends BaseService
{

    protected function createModeration()
    {
        return new Moderation();
    }

    /**
     * Get the latest moderation actions
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return Paginator
     */
    public function getLatestModerations($offset, $limit)
    {
        return $this->em
                    ->getRepository($this->moderationRepositoryClass)
                    ->getLatestModerations($offset, $limit);
    }

    /**
     * Create a moderation entry when an entity is deleted
     * 
     * @param  Board|Topic|Message  $entity
     * @param  UserInterface        $user
     * 
     * @return Moderation
     */
    public function delete($entity, UserInterface $user)
    {
        if (false === $entity instanceof Board 
            && false === $entity instanceof Topic
            && false === $entity instanceof Message) {
            throw new \InvalidArgumentException('Entity should be either a Board entity, a Topic entity or a Message entity.');
        }

        $moderation = $this->createFromAction('delete', $user);

        $flag = null;

        if (true === $entity instanceof Board) {
            $moderation->setBoard($entity);
        }

        if (true === $entity instanceof Topic) {
            $moderation->setTopic($entity);
            $flag = $this->container->get('cornichon.forum.flag')->getByTopic($entity);
        }

        if (true === $entity instanceof Message) {
            $moderation->setMessage($entity);
            $flag = $this->container->get('cornichon.forum.flag')->getByMessage($entity);
        }

        $this->save($moderation);

        // If the item is currently flagged and the flag hasn't been moderated
        if ($flag !== null && $flag->setModeration() === null) {
            $flag->setModeration($moderation);
            $this->container->get('cornichon.forum.flag')->save($flag);
        }

        return $moderation;
    }

    /**
     * Create a moderation entry when an entity is deleted
     * 
     * @param  Board|Topic|Message  $entity
     * @param  UserInterface        $user
     * 
     * @return Moderation
     */
    public function undelete($entity, UserInterface $user)
    {
        if (false === $entity instanceof Board 
            && false === $entity instanceof Topic
            && false === $entity instanceof Message) {
            throw new \InvalidArgumentException('Entity should be either a Board entity, a Topic entity or a Message entity.');
        }

        $moderation = $this->createFromAction('undelete', $user);

        if (true === $entity instanceof Board) {
            $moderation->setBoard($entity);
        }

        if (true === $entity instanceof Topic) {
            $moderation->setTopic($entity);
        }

        if (true === $entity instanceof Message) {
            $moderation->setMessage($entity);
        }

        $this->save($moderation);

        return $moderation;
    }

    /**
     * Create a moderation entry when an entity is locked
     * 
     * @param  Topic           $entity
     * @param  UserInterface   $user
     * 
     * @return Moderation
     */
    public function lock(TopicInterface $topic, UserInterface $user)
    {
        $moderation = $this->createFromAction('lock', $user);
        $moderation->setTopic($topic);

        $this->save($moderation);

        return $moderation;
    }

    /**
     * Create a moderation entry when an entity is unlocked
     * 
     * @param  Topic           $entity
     * @param  UserInterface   $user
     * 
     * @return Moderation
     */
    public function unlock(TopicInterface $topic, UserInterface $user)
    {
        $moderation = $this->createFromAction('unlock', $user);
        $moderation->setTopic($topic);

        $this->save($moderation);

        return $moderation;
    }

    /**
     * Create a moderation entry when an entity is pinned
     * 
     * @param  Topic           $entity
     * @param  UserInterface   $user
     * 
     * @return Moderation
     */
    public function pin(TopicInterface $topic, UserInterface $user)
    {
        $moderation = $this->createFromAction('pin', $user);
        $moderation->setTopic($topic);

        $this->save($moderation);

        return $moderation;
    }

    /**
     * Create a moderation entry when an entity is unpinned
     * 
     * @param  Topic           $entity
     * @param  UserInterface   $user
     * 
     * @return Moderation
     */
    public function unpin(TopicInterface $topic, UserInterface $user)
    {
        $moderation = $this->createFromAction('unpin', $user);
        $moderation->setTopic($topic);

        $this->save($moderation);

        return $moderation;
    }

    /**
     * The generic function that creates a moderation object
     * 
     * @param  string          $action
     * @param  UserInterface   $user
     * 
     * @return Moderation
     */
    private function createFromAction($action, UserInterface $user)
    {
        $moderation = $this->createModeration();

        switch ($action) {
            case 'pin':
                $moderation->setActionId(Moderation::ACTION_ID_PIN);
                break;
            case 'unpin':
                $moderation->setActionId(Moderation::ACTION_ID_UNPIN);
                break;
            case 'delete':
                $moderation->setActionId(Moderation::ACTION_ID_DELETE);
                break;
            case 'undelete':
                $moderation->setActionId(Moderation::ACTION_ID_UNDELETE);
                break;
            case 'lock':
                $moderation->setActionId(Moderation::ACTION_ID_LOCK);
                break;
            case 'unlock':
                $moderation->setActionId(Moderation::ACTION_ID_UNLOCK);
                break;
            case 'move':
                $moderation->setActionId(Moderation::ACTION_ID_MOVE);
                break;
        }
        
        $moderation->setDateCreated(new \DateTime());
        $moderation->setUser($user);

        return $moderation;
    }

    /**
     * Method used to save a moderation object
     * 
     * @param  Moderation $moderation
     * 
     * @return Moderation
     */
    public function save(ModerationInterface $moderation)
    {
        $this->em->persist($moderation);
        $this->em->flush();

        return $moderation;
    }

}