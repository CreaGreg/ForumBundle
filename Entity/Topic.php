<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cornichon\ForumBundle\Entity\Topic
 *
 * @ORM\MappedSuperclass
 */
class Topic
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=128)
     */
    protected $title;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=32)
     */
    protected $slug;

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
     * @var boolean $isDeleted
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted = false;

    /**
     * @var boolean $isLocked
     *
     * @ORM\Column(name="is_locked", type="boolean")
     */
    protected $isLocked = false;

    /**
     * @var boolean $isPinned
     *
     * @ORM\Column(name="is_pinned", type="boolean")
     */
    protected $isPinned = false;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var \Cornichon\ForumBundle\Entity\TopicStat $stat
     */
    protected $stat;

    /**
     * @var \Cornichon\ForumBundle\Entity\Board $board
     */
    protected $board;

    /**
     * @var ArrayCollection
     */
    protected $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
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
     * @return Topic
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
     * Set slug
     *
     * @param string $slug
     * @return Topic
     */
    public function setSlug($slug = null)
    {
        if ($slug === null) {
            // Found on Stackoverflow ~ originate from Symfony1 Jobeet
            // replace non letter or digits by -
            $slug = preg_replace('~[^\\pL\d]+~u', '-', $this->title);
            // trim
            $slug = trim($slug, '-');
            // transliterate
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
            // lowercase
            $slug = strtolower($slug);
            // remove unwanted characters
            $slug = preg_replace('~[^-\w]+~', '', $slug);
        }

        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Topic
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
     * @return Topic
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
     * @return Topic
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
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return Topic
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    
        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean 
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set isPinned
     *
     * @param boolean $isPinned
     * @return Topic
     */
    public function setIsPinned($isPinned)
    {
        $this->isPinned = $isPinned;
    
        return $this;
    }

    /**
     * Get isPinned
     *
     * @return boolean 
     */
    public function isPinned()
    {
        return $this->isPinned;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return Topic
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
     * @param \Cornichon\ForumBundle\Entity\TopicStat $stat
     * @return Topic
     */
    public function setStat(\Cornichon\ForumBundle\Entity\TopicStat $stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return \Cornichon\ForumBundle\Entity\TopicStat
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * Set board
     *
     * @param \Cornichon\ForumBundle\Entity\Board $board
     * @return Topic
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
     * Set messages
     *
     * @return Topic
     */
    public function setMessages(ArrayCollection $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get a collection of messages
     *
     * @return ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
