<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cornichon\ForumBundle\Entity\UserStat
 *
 * @ORM\MappedSuperclass
 */
class UserStat
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
     * @var integer $totalMessage
     *
     * @ORM\Column(name="total_message", type="integer")
     */
    protected $totalMessage = 0;

    /**
     * @var integer $totalTopic
     *
     * @ORM\Column(name="total_topic", type="integer")
     */
    protected $totalTopic = 0;

    /**
     * @var User $user
     */
    protected $user;

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
     * Set totalMessage
     *
     * @param integer $totalMessage
     * 
     * @return UserStat
     */
    public function setTotalMessage($totalMessage)
    {
        $this->totalMessage = $totalMessage;
    
        return $this;
    }

    /**
     * Get totalMessage
     *
     * @return integer 
     */
    public function getTotalMessage()
    {
        return $this->totalMessage;
    }

    /**
     * Increase the total of messages created by the user
     * 
     * @param  integer $int = 1
     * 
     * @return UserStat
     */
    public function increaseTotalMessage($int = 1)
    {
        $this->totalMessage += $int;

        return $this;
    }

    /**
     * Decrease the total of messages
     * 
     * @param  integer $int = 1
     * 
     * @return UserStat
     */
    public function decreaseTotalMessage($int = 1)
    {
        $this->totalMessage -= $int;

        return $this;
    }

    /**
     * Set totalTopic
     *
     * @param integer $totalTopic
     * 
     * @return UserStat
     */
    public function setTotalTopic($totalTopic)
    {
        $this->totalTopic = $totalTopic;
    
        return $this;
    }

    /**
     * Get totalTopic
     *
     * @return integer 
     */
    public function getTotalTopic()
    {
        return $this->totalTopic;
    }

    /**
     * Increase the total of topics created by the user
     * 
     * @param  integer $int = 1
     * 
     * @return UserStat
     */
    public function increaseTotalTopic($int = 1)
    {
        $this->totalTopic += $int;

        return $this;
    }

    /**
     * Decrease the total of topics created by the user
     * 
     * @param  integer $int = 1
     * 
     * @return  UserStat
     */
    public function decreaseTotalTopic($int = 1)
    {
        $this->totalTopic -= $int;

        return $this;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * 
     * @return UserStat
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
}
