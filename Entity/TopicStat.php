<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cornichon\UserBundle\Entity\User;

/**
 * Cornichon\ForumBundle\Entity\TopicStat
 *
 * @ORM\Table(name="`topic_stat`")
 * @ORM\Entity
 */
class TopicStat
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $views
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var integer $posts
     *
     * @ORM\Column(name="posts", type="integer")
     */
    private $posts;

    /**
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime")
     */
    private $dateModified;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="\Cornichon\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $lastUser;


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
     * Set views
     *
     * @param integer $views
     * @return TopicStat
     */
    public function setViews($views)
    {
        $this->views = $views;
    
        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set posts
     *
     * @param integer $posts
     * @return TopicStat
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    
        return $this;
    }

    /**
     * Get posts
     *
     * @return integer 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return TopicStat
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
     * @return TopicStat
     */
    public function setLastUser(UserInterface $user)
    {
        $this->lastUser = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getLastUser()
    {
        return $this->lastUser;
    }
}
