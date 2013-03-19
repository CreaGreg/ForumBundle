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
     * @var integer $posts
     *
     * @ORM\Column(name="total_posts", type="integer")
     */
    protected $totalPosts = 0;

    /**
     * @var integer $topics
     *
     * @ORM\Column(name="total_topics", type="integer")
     */
    protected $totalTopics = 0;

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
     * Set total posts
     *
     * @param integer $int
     * 
     * @return Board
     */
    public function setTotalPosts($int)
    {
        $this->totalPosts = $int;
    
        return $this;
    }

    /**
     * Get total posts
     *
     * @return integer 
     */
    public function getTotalPosts()
    {
        return $this->totalPosts;
    }

    /**
     * Increase the total of posts in the topic
     * 
     * @param  integer $int = 1
     * 
     * @return Board
     */
    public function increaseTotalPosts($int = 1)
    {
        $this->totalPosts += $int;

        return $this;
    }

    /**
     * Decrease the total of posts in the topic
     * 
     * @param  integer $int = 1
     * 
     * @return  Board
     */
    public function decreaseTotalPosts($int = 1)
    {
        $this->totalPosts -= $int;

        return $this;
    }

    /**
     * Set totalTopics
     *
     * @param integer $int
     * @return BoardStat
     */
    public function setTotalTopics($int)
    {
        $this->totalTopics = $int;
    
        return $this;
    }

    /**
     * Get totalTopics
     *
     * @return integer 
     */
    public function getTotalTopics()
    {
        return $this->totalTopics;
    }

    /**
     * Increase the total of topics in the topic
     * 
     * @param  integer $int = 1
     * 
     * @return  Board
     */
    public function increaseTotalTopics($int = 1)
    {
        $this->totalTopics += $int;

        return $this;
    }

    /**
     * Decrease the total of topics in the topic
     * 
     * @param  integer $int = 1
     * 
     * @return  Board
     */
    public function decreaseTotalTopics($int = 1)
    {
        $this->totalTopics -= $int;

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
    public function setParent(\Cornichon\ForumBundle\Entity\Board $parent = null)
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

    /**
     * Convenient methods
     */
    
    /**
     * Get this class' name
     * 
     * @return string
     */
    public function getClassName()
    {
        return 'board';
    }

    /**
     * Get a short value of the number of posts
     *
     * @return string
     */
    public function getShortTotalPosts()
    {
        return $this->convertFormat($this->totalPosts);
    }

    /**
     * Get a short value of the number of topics
     *
     * @return string
     */
    public function getShortTotalTopics()
    {
        return $this->convertFormat($this->totalTopics);
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
     * 
     * @return string
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
