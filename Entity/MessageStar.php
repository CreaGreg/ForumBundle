<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cornichon\ForumBundle\Entity\MessageStar
 *
 * @ORM\MappedSuperclass
 */
class MessageStar implements MessageStarInterface
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
     * @var boolean $isDeleted
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted = false;

    /**
     * @var User $user
     */
    protected $user;

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
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Message
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    
        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return Message
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
     * Set message
     *
     * @param \Cornichon\ForumBundle\Entity\Message $message
     * 
     * @return MessageStar
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

    //
    // CONVENIENT METHODS
    //

    /**
     * Get this class' name
     * 
     * @return string
     */
    public function getClassName()
    {
        return 'message_star';
    }
}
