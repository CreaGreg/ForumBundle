<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cornichon\ForumBundle\Entity\TopicStat
 *
 * @ORM\MappedSuperclass
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
    protected $id;

    /**
     * @var integer $views
     *
     * @ORM\Column(name="views", type="integer")
     */
    protected $views = 0;

    /**
     * @var integer $posts
     *
     * @ORM\Column(name="posts", type="integer")
     */
    protected $posts = 0;

    /**
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $dateModified;

    /**
     * @var \DateTime $lastMessageDate
     * @ORM\Column(name="last_message_date", type="datetime", nullable=true)
     */
    protected $lastMessageDate;

    /**
     * @var User $user
     */
    protected $lastUser;

    /**
     * @var \Cornichon\ForumBundle\Entity\Topic $topic
     */
    protected $topic;


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
    public function setLastUser(\Symfony\Component\Security\Core\User\UserInterface $user)
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

    /**
     * Set lastMessageDate
     *
     * @param \DateTime $lastMessageDate
     * @return TopicStat
     */
    public function setLastMessageDate($lastMessageDate)
    {
        $this->lastMessageDate = $lastMessageDate;
    
        return $this;
    }

    /**
     * Get lastMessageDate
     *
     * @return \DateTime 
     */
    public function getLastMessageDate()
    {
        return $this->lastMessageDate;
    }

    /**
     * Set topic
     *
     * @param \Cornichon\ForumBundle\Entity\Topic $topic
     * @return TopicStat
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
     * Convenient methods
     */

    /**
     * Get a short value of the number of posts
     *
     * @return string
     */
    public function getShortPosts()
    {
        return $this->convertFormat($this->posts);
    }

    /**
     * Get a short value of the number of views
     *
     * @return string
     */
    public function getShortViews()
    {
        return $this->convertFormat($this->views);
    }

    /**
     * Convert an integer into a short string
     *
     * 1000 -> 1K
     * 1500 -> 1.5K
     * 150500 -> 150K
     * 1000000 -> 1M
     * 1500000 -> 1.5M
     *
     * @param  integer  $var
     * @return integer
     */
    protected function convertFormat($var)
    {   
        if ($var < 1000) {
            return $var;
        }
        else if ($var < 10000) {
            return round($var / 1000, 1) ."K";
        }
        else if ($var < 1000000) {
            return round($var / 1000) ."K";
        }
        else if ($var < 1000000) {
            return round($var / 1000000) . "M";
        }
    }
}
