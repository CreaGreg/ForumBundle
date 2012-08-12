<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cornichon\ForumBundle\Entity\Board
 *
 * @ORM\Table(name="`board`")
 * @ORM\Entity(repositoryClass="\Cornichon\ForumBundle\Repository\BoardRepository")
 */
class Board
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=64)
     */
    private $title;

    /**
     * @var string $shortTitle
     *
     * @ORM\Column(name="short_title", type="string", length=8)
     */
    private $shortTitle;

    /**
     * @var string $body
     *
     * @ORM\Column(name="body", type="string", length=255, nullable=true)
     */
    private $body;

    /**
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    private $dateModified;

    /**
     * @var boolean $isDeleted
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted = false;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="\Cornichon\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \Cornichon\ForumBundle\Entity\BoardStat $stat
     *
     * @ORM\OneToOne(targetEntity="\Cornichon\ForumBundle\Entity\BoardStat", mappedBy="board")
     */
    private $stat;

    /**
     * @var ArrayCollection $topics
     *
     * @ORM\OneToMany(targetEntity="\Cornichon\ForumBundle\Entity\Topic", mappedBy="board")
     */
    private $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Board
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set shortTitle
     *
     * @param string $shortTitle
     * @return Board
     */
    public function setShortTitle($shortTitle)
    {
        $this->shortTitle = $shortTitle;
    
        return $this;
    }

    /**
     * Get shortTitle
     *
     * @return string 
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Board
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Board
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
     * @return Board
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
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Board
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
     * @return Board
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
     * Set stat
     *
     * @param \Cornichon\ForumBundle\Entity\BoardStat $stat
     * @return Board
     */
    public function setStat(\Cornichon\ForumBundle\Entity\BoardStat $stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return \Cornichon\ForumBundle\Entity\BoardStat
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * Set topics
     *
     * @return Board
     */
    public function setTopics(ArrayCollection $topics)
    {
        $this->topics = $topics;

        return $this;
    }

    /**
     * Get a collection of topics
     *
     * @return ArrayCollection
     */
    public function getTopics()
    {
        return $this->topics;
    }
}
