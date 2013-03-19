<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Flag
 *
 * @ORM\MappedSuperclass
 */
class Flag
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var integer $totalFlagged
     *
     * @ORM\Column(name="total_flagged", type="integer")
     */
    protected $totalFlagged = 0;

    /**
     * @var ArrayCollection $users
     */
    protected $users;

    /**
     * @var \Cornichon\ForumBundle\Entity\Topic $topic
     */
    protected $topic;

    /**
     * @var \Cornichon\ForumBundle\Entity\Message $message
     */
    protected $message;

    /**
     * @var \Cornichon\ForumBundle\Entity\Moderation $moderation
     */
    protected $moderation;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Flag
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set totalFlagged
     *
     * @param integer $int
     * 
     * @return Flag
     */
    public function setTotalFlagged($int)
    {
        $this->totalFlagged = $int;
    
        return $this;
    }

    /**
     * Get totalFlagged
     *
     * @return integer 
     */
    public function getTotalFlagged()
    {
        return $this->totalFlagged;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return Moderation
     */
    public function addUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove a user entity from the collection
     * 
     * @param  \Symfony\Component\Security\Core\User\UserInterface      $user
     * 
     * @return ArrayCollection
     */
    public function removeUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->users->removeElement($user);

        return $this->users;
    }

    /**
     * Get a list of users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set message
     *
     * @param \Cornichon\ForumBundle\Entity\Message $message
     * 
     * @return Flag
     */
    public function setMessage(\Cornichon\ForumBundle\Entity\Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Cornichon\ForumBundle\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set topic
     *
     * @param \Cornichon\ForumBundle\Entity\Topic $topic
     * 
     * @return Flag
     */
    public function setTopic(\Cornichon\ForumBundle\Entity\Topic $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \Cornichon\ForumBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set moderation
     *
     * @param \Cornichon\ForumBundle\Entity\Moderation $moderation
     * 
     * @return Flag
     */
    public function setModeration(\Cornichon\ForumBundle\Entity\Moderation $moderation)
    {
        $this->moderation = $moderation;

        return $this;
    }

    /**
     * Get moderation
     *
     * @return \Cornichon\ForumBundle\Entity\Moderation
     */
    public function getModeration()
    {
        return $this->moderation;
    }

    /**
     * Returns the entity associated to this flag
     * 
     * @return Message|Topic
     */
    public function getFlaggedItem()
    {
        if ($this->getMessage() !== null) {
            return $this->getMessage();
        }
        else if ($this->getTopic() !== null) {
            return $this->getTopic();
        }
    }

    /**
     * Returns the name of the moderated item
     * 
     * @return string
     */
    public function getFlaggedItemName()
    {
        return $this->getFlaggedItem()->getClassName();
    }
}
