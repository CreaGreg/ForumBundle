<?php

namespace Cornichon\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cornichon\ForumBundle\Entity\Board
 *
 * @ORM\MappedSuperclass
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
    protected $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=64)
     */
    protected $title;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=64)
     */
    protected $slug;

    /**
     * @var string $shortTitle
     *
     * @ORM\Column(name="short_title", type="string", length=16)
     */
    protected $shortTitle;

    /**
     * @var string $body
     *
     * @ORM\Column(name="body", type="string", length=255, nullable=true)
     */
    protected $body;

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
     * @var ArrayCollection $children
     */
    protected $children;

    /**
     * @var \Cornichon\ForumBundle\Entity\Board $parent
     */
    protected $parent;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var \Cornichon\ForumBundle\Entity\BoardStat $stat
     */
    protected $stat;

    /**
     * @var ArrayCollection $topics
     */
    protected $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->children = new ArrayCollection();
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
     * Set slug
     *
     * @param string $slug
     * @return Board
     */
    public function setSlug($slug = null)
    {
        if ($slug === null) {
            // Found on Stackoverflow ~ originate from Symfony1 Jobeet
            // replace non letter or digits by -
            $slug = preg_replace('~[^\\pL\d]+~u', '-', $this->shortTitle);
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

    /**
     * Add board child
     *
     * @param Board $board
     * @return Board
     */
    public function addChild(Board $board)
    {
        $this->children[] = $board;

        return $this;
    }

    /**
     * Set children
     *
     * @return Board
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get a collection of children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set board
     *
     * @param \Cornichon\ForumBundle\Entity\Board $parent
     * @return Board
     */
    public function setParent(\Cornichon\ForumBundle\Entity\Board $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get board
     *
     * @return \Cornichon\ForumBundle\Entity\Board
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get parent boards
     *
     * @return ArrayCollection
     */
    public function getParents()
    {
        $parents = array();

        $this->loadParent($this, $parents);

        return $parents;
    }

    /**
     * Load the parent and its predecessors
     */
    private function loadParent($board, &$parents)
    {
        if ($board->getParent() !== null) {
            $this->loadParent($board->getParent(), $parents);
            $parents[] = $board->getParent();
        }
    }
}
