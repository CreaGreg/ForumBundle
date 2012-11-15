<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Doctrine\Common\Collections\ArrayCollection;

class BoardService extends BaseService
{

    protected function createBoardStat()
    {
        return new BoardStat();
    }

    protected function createBoard()
    {
        return new Board();
    }

    /**
     * Get a Board entity
     * 
     * @param  integer  $boardId
     * 
     * @return Board|null
     */
    public function getById($boardId)
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->find($boardId);
    }

    /**
     * Get a collection of the main boards
     * 
     * @return ArrayCollection
     */
    public function getMainBoards()
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getMainBoards();
    }

    /**
     * Get a list of boards by parent board
     * 
     * @param  Board  $board
     * 
     * @return ArrayCollection
     */
    public function getBoardsByParentBoard(Board $board)
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getBoardsByParentBoard($board);
    }

    /**
     * Get a list of boards by parent boards
     * 
     * @param  ArrayCollection $boards
     * 
     * @return ArrayCollection
     */
    public function getBoardsByParentBoards(ArrayCollection $boards)
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getBoardsByParentBoards($boards);
    }

    /**
     * Gets all boards and build up the full hierarchie
     * 
     * @param  boolean  $deleted = false
     * 
     * @return ArrayCollection
     */
    public function getBoards($deleted = false)
    {
        /**
         * Get all the board id and their parent ids
         */
        $ids = $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getBoardIdsRaw($deleted);

        /**
         * Create a map array board_id => parent_id
         */
        $map = array();
        foreach ($ids as $id) {
            $map[$id['parent_id']][] = $id['id'];
        }

        /**
         * Get the main boards to use as a start point
         */
        $mainBoards = $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getMainBoards($deleted);

        /**
         * Get all the boards
         */
        $boards = $this->em
                        ->getRepository($this->boardRepositoryClass)
                        ->getBoards($deleted);
        /**
         * Reorganize and make it a map
         */
        $allBoards = array();
        foreach ($boards as $board) {
            $allBoards[$board->getId()] = $board;
        }

        $this->processLayer($map, $mainBoards, $allBoards);

        return $mainBoards;
    }

    /**
     * Build an array of ids from all children of a board
     * and recursively get the children of the children
     * 
     * @param  Board  $board
     * 
     * @return array
     */
    public function getChildrenIdsFromBoard(Board $board)
    {
        $ids = array();

        foreach ($board->getChildren() as $b) {
            $ids[] = $b->getId();

            if ($b->getChildren()->count() !== 0) {
                $ids = array_merge($ids, $this->getChildrenIdsFromBoard($b));
            }
        }

        return $ids;
    }

    /**
     * Process each layer of the hierarchy
     * 
     * @param  array           $map       array(parentId => array(boardId, boardId, ...))
     * @param  ArrayCollection $boards    collection of boards with the same parent board
     * @param  array           $allBoards array(boardId => Board)
     * 
     * @return ArrayCollection
     */
    private function processLayer(array $map, ArrayCollection $boards, array $allBoards)
    {
        $coll = new ArrayCollection();
        foreach ($boards as $board) {

            if (array_key_exists($board->getId(), $map) === true) {

                $tmpColl = new ArrayCollection();

                foreach ($map[$board->getId()] as $boardId) {
                    $tmpColl->add($allBoards[$boardId]);
                }

                $board->setChildren(
                    $this->processLayer($map, $tmpColl, $allBoards)
                );
            }
            else {
                $board->setChildren(new ArrayCollection());
            }

            $coll->add($board);

        }
        return $coll;
    }

    /**
     * Get the latest boards based on the board id
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestBoards($offset, $limit)
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getLatestBoards($offset, $limit);
    }

    /**
     * Get the popular boards based on the number of posts
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPopularBoards($offset, $limit)
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->getPopularBoards($offset, $limit);
    }

    /**
     * Gets all boards
     * Careful! If you have thousands of boards, your server will run out of memory
     * 
     * @return ArrayCollection
     */
    public function getAll()
    {
        return new ArrayCollection($this->em->getRepository($this->boardRepositoryClass)->findAll());
    }

    /**
     * Delete a board
     * Throws errors if the board is not clean
     * 
     * @param  Board  $board
     * 
     * @throws \Cornichon\ForumBundle\Exception\TopicExistsException
     * @throws \Cornichon\ForumBundle\Exception\BoardExistsException
     */
    public function delete(Board $board)
    {
        $boards = $this->getBoardsByParentBoard($board);

        if ($boards->count() !== 0) {
            throw new \Cornichon\ForumBundle\Exception\BoardExistsException();
        }

        $topics = $this->container->get('cornichon.forum.topic')->getLatestTopicsByBoard($board, 0, 1);

        if ($topics->count() !== 0) {
            throw new \Cornichon\ForumBundle\Exception\TopicExistsException();
        }

        $this->em->remove($board->getStat());
        $this->em->remove($board);
        $this->em->flush();
    }

    /**
     * Save a board and make sure all associations are built properly
     * 
     * @param  Board  $board
     * 
     * @return Board
     */
    public function save(Board $board)
    {
        // Pick a user if none was specified
        if ($board->getUser() === null) {
            $board->setUser($this->container->get('security.context')->getToken()->getUser());
        }

        // If the board slug hasn't been set, we set one
        if ($board->getSlug() === null) {
            $board->setSlug();
        }

        // If the board is new
        if ($board->getId() === null) {
            $board->setDateCreated(new \DateTime());
        }
        else {
            $board->getDateModified(new \DateTime());
        }

        $this->em->persist($board);

        if ($board->getStat() === null) {
            $boardStat = $this->createBoardStat();
            $boardStat->setBoard($board);
            $this->em->persist($boardStat);

            $board->setStat($boardStat);
        }
        
        $this->em->flush();

        return $board;
    }

    /**
     * Build a single slug for a board
     * It will go through the parent slug and attach them together
     * 
     * @param  Board  $board
     * 
     * @return string
     */
    public function buildSlug(Board $board)
    {
        $parents = $board->getParents();

        $boardSlug = "";
        foreach ($parents as $parent) {
            $boardSlug .= $parent->getSlug() ."-";
        }
        $boardSlug .= $board->getSlug();

        return $boardSlug;
    }

    /**
     * Change the parent board of all boards that has $original as a parent
     * 
     * @param  Board  $original 
     * @param  Board  $destination 
     * 
     * @return integer
     */
    public function switchBoardParent(Board $original, Board $destination)
    {
        return $this->em
                    ->getRepository($this->boardRepositoryClass)
                    ->switchBoardParent($original, $destination);
    }

    /**
     * Add a given number to the topics count of a given board
     * and bubbles up to its parents
     * 
     * @param  Board   $board
     * @param  integer $increment = 1
     */
    public function incrementStatTopics(Board $board, $increment = 1)
    {
        $board->getStat()->setTopics(
            $board->getStat()->getTopics() + $increment
        );

        $this->em->persist($board);

        if ($board->getParent() !== null) {
            $this->incrementStatTopics($board->getParent());
        }
    }

    /**
     * Add a given number to the posts count of a given board
     * and bubbles up to its parents
     * 
     * @param  Board   $board    
     * @param  integer $increment = 1
     */
    public function incrementStatPosts(Board $board, $increment = 1) 
    {
        $board->getStat()->setPosts(
            $board->getStat()->getPosts() + $increment
        );

        $this->em->persist($board);

        if ($board->getParent() !== null) {
            $this->incrementStatPosts($board->getParent());
        }
    }

    public function moveBoard(Board $original, Board $destination)
    {
        
    }

    /**
     * Move the content of the original board into a destination board
     * 
     * @param  Board  $original    
     * @param  Board  $destination
     * 
     * @throws \Cornichon\ForumBundle\Exception\InvalidBoardException
     */
    public function moveContent(Board $original, Board $destination)
    {
        if ($original->getId() === $destination->getId()) {
            throw new \Cornichon\ForumBundle\Exception\InvalidBoardException();
        }

        $posts = $original->getStat()->getPosts();
        $topics = $original->getStat()->getTopics();

        // Make sure the destination is not the children of the source
        $board = $destination->getParent();
        while ($board !== null) {
            if ($board->getId() === $original->getId()) {
                throw new \Cornichon\ForumBundle\Exception\InvalidBoardException();
            }
            $board = $board->getParent();
        }

        $outOfScope = true;
        if ($original->getParent() !== null) {
           $outOfScope = $this->recurciveMoveNegation($original->getParent(), $destination, $posts, $topics);
        }

        if ($outOfScope === true) {
            $this->recurciveMoveAddition($destination, $posts, $topics);
        }

        // Move topics
        $this->container
             ->get('cornichon.forum.topic')
             ->moveFromBoardToBoard($original, $destination);

        // Move boards
        $this->switchBoardParent($original, $destination);

        $this->em->flush();
    }

    /**
     * Progress through the parents of the given boards and decrease their 
     * stats with given post and topic counts.
     * If the destination board is reached it will stop recursive calls and return false
     * If the destionation board is not reached it will return true
     * 
     * @param  Board   $source      
     * @param  Board   $destination 
     * @param  integer $posts       
     * @param  integer $topics
     *    
     * @return boolean
     */
    private function recurciveMoveNegation($source, $destination, $posts, $topics)
    {
        if ($source->getId() === $destination->getId()) {
            return false;
        }
        else {
            $source->getStat()->setPosts(
                $source->getStat()->getPosts() - $posts
            );
            $source->getStat()->setTopics(
                $source->getStat()->getTopics() - $topics
            );
            $this->em->persist($source->getStat());
        }

        if ($source->getParent() !== null) {
            return $this->recurciveMoveNegation($source->getParent(), $destination, $posts, $topics);
        }
        else {
            return true;
        }
    }

    /**
     * Progress through the parents of the given boards and increase their 
     * stats with given post and topic counts
     * 
     * @param  Board   $board
     * @param  integer $posts
     * @param  integer $topics
     * 
     * @return boolean
     */
    private function recurciveMoveAddition($board, $posts, $topics)
    {
        $board->getStat()->setPosts(
            $board->getStat()->getPosts() + $posts
        );
        $board->getStat()->setTopics(
            $board->getStat()->getTopics() + $topics
        );
        
        $this->em->persist($board->getStat());

        if ($board->getParent() !== null) {
            return $this->recurciveMoveAddition($board->getParent(), $posts, $topics);
        }
        else {
            return true;
        }
    }
}