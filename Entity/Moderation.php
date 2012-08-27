<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cornichon\ForumBundle\Entity\Moderation
 *
 * @ORM\MappedSuperclass
 */
class Moderation
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
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $dateModified;

    /**
     * @var bool $isValid
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=true)
     */
    protected $isValid;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var User $moderator
     */
    protected $moderator;

    /**
     * @var \Cornichon\ForumBundle\Entity\Topic $topic
     */
    protected $topic;

    /**
     * @var \Cornichon\ForumBundle\Entity\Message $message
     */
    protected $message;

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
     * Set isValid
     *
     * @param boolean $isValid
     * @return Moderation
     */
    public function setIsValid($isValid = null)
    {
        $this->isValid = $isValid;
    
        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean 
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Moderation
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
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Moderation
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return Moderation
     */
    public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set moderator
     *
     * @param UserInterface $moderator
     * @return Moderation
     */
    public function setModerator(\Symfony\Component\Security\Core\User\UserInterface $moderator)
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * Get moderator
     *
     * @return UserInterface
     */
    public function getModerator()
    {
        return $this->moderator;
    }

    /**
     * Set message
     *
     * @param \Cornichon\ForumBundle\Entity\Message $message
     * @return Moderation
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
     * @return Moderation
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

}