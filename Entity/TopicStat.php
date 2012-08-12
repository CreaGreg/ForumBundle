<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $views = 0;

    /**
     * @var integer $posts
     *
     * @ORM\Column(name="posts", type="integer")
     */
    private $posts = 0;

    /**
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
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
     * @var \Cornichon\ForumBundle\Entity\Topic $topic
     *
     * @ORM\OneToOne(targetEntity="\Cornichon\ForumBundle\Entity\Topic", inversedBy="stat")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     */
    private $topic;


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
}
