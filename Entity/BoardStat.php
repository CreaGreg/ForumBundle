<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cornichon\ForumBundle\Entity\BoardStat
 *
 * @ORM\Table(name="`board_stat`")
 * @ORM\Entity
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
    private $id;

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
     * @var \Cornichon\ForumBundle\Entity\Board $board
     *
     * @ORM\OneToOne(targetEntity="\Cornichon\ForumBundle\Entity\Board", inversedBy="stat")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     */
    private $board;

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
}
