<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cornichon\ForumBundle\Entity\BoardStat
 *
 * @ORM\MappedSuperclass
 */
class BoardStat
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
     * @var integer $posts
     *
     * @ORM\Column(name="posts", type="integer")
     */
    protected $posts = 0;

    /**
     * @var integer $topics
     *
     * @ORM\Column(name="topics", type="integer")
     */
    protected $topics = 0;

    /**
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $dateModified;

    /**
     * @var \Cornichon\ForumBundle\Entity\Board $board
     */
    protected $board;

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
     * Set posts
     *
     * @param integer $posts
     * @return BoardStat
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
     * Set topics
     *
     * @param integer $topics
     * @return BoardStat
     */
    public function setTopics($topics)
    {
        $this->topics = $topics;
    
        return $this;
    }

    /**
     * Get topics
     *
     * @return integer 
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return BoardStat
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
     * Set board
     *
     * @param \Cornichon\ForumBundle\Entity\Board $board
     * @return BoardStat
     */
    public function setBoard(\Cornichon\ForumBundle\Entity\Board $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return \Cornichon\ForumBundle\Entity\Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Convenient methods
     */

    /**
     * Get a short value of the number of posts
     * 1000 -> 1K
     * 1500 -> 1.5K
     * 150500 -> 150K
     * 1000000 -> 1M
     * 1500000 -> 1.5M
     *
     * @return integer
     */
    public function getShortPosts()
    {
        if ($this->posts < 1000) {
            return $this->posts;
        }
        else if ($this->posts < 10000) {
            return round($this->posts / 1000, 1) ."K";
        }
        else if ($this->posts < 1000000) {
            return round($this->posts / 1000) ."K";
        }
        else if ($this->posts < 1000000) {
            return round($this->posts / 1000000) . "M";
        }
    }

    /**
     * Get a short value of the number of topics
     * 1000 -> 1K
     * 1500 -> 1.5K
     * 150500 -> 150K
     * 1000000 -> 1M
     * 1500000 -> 1.5M
     *
     * @return integer
     */
    public function getShortTopics()
    {
        if ($this->topics < 1000) {
            return $this->topics;
        }
        else if ($this->topics < 10000) {
            return round($this->topics / 1000, 1) ."K";
        }
        else if ($this->topics < 1000000) {
            return round($this->topics / 1000) ."K";
        }
        else if ($this->topics < 1000000) {
            return round($this->topics / 1000000) . "M";
        }
    }
}
