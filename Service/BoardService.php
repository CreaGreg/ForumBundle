<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Doctrine\Common\Collections\ArrayCollection;

class BoardService extends BaseService {

	protected function createBoardStat()
	{
		return new BoardStat();
	}

	public function getById($boardId)
	{
		return $this->em
					->getRepository($this->boardRepositoryClass)
					->find($boardId);
	}

	public function getMainBoards()
	{
		return $this->em
			            ->getRepository($this->boardRepositoryClass)
			            ->getMainBoards();
	}

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

	private function processLayer($map, $boards, $allBoards)
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

	public function getLatestBoards($offset, $limit)
	{
		return $this->em
					->getRepository($this->boardRepositoryClass)
					->getLatestBoards($offset, $limit);
	}

	public function getPopularBoards($offset, $limit)
	{
		return $this->em
					->getRepository($this->boardRepositoryClass)
					->getPopularBoards($offset, $limit);
	}

	public function getAll()
	{
		return new ArrayCollection($this->em->getRepository($this->boardRepositoryClass)->findAll());
	}

	public function save ($board)
	{
		if ($board->getUser() === null) {
			$board->setUser(
				$this->container->get('security.context')->getToken()->getUser()
			);
		}

		$board->setSlug();

		if ($board->getId() === null) {
			$board->setDateCreated(new \DateTime());
		}
		else {
			$board->getDateModified(new \DateTime());
		}

		$this->em->persist($board);
		$this->em->flush($board);

		if ($board->getStat() === null) {
			$boardStat = $this->createBoardStat();
			$boardStat->setBoard($board);
			$this->em->persist($boardStat);

			$board->setStat($boardStat);
		}
		
		$this->em->flush();

		return $board;
	}

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

    public function changeBoardParent(Board $original, Board $destination)
    {
		return $this->em
					->getRepository($this->boardRepositoryClass)
					->changeBoardParent($original, $destination);
    }

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

    public function moveContent(Board $original, Board $destination)
    {
    	$posts = $original->getStat()->getPosts();
    	$topics = $original->getStat()->getTopics();

    	// Make sure the destination is not the children of the source
    	$board = $destination->getParent();
    	do {
    		if ($board->getId() === $original->getId()) {
    			throw new \Cornichon\ForumBundle\Exception\InvalidBoardException();
    		}
    		$board = $board->getParent();
    	} while ($board !== null);

    	$outOfScope = $this->recurciveMoveNegation($original->getParent(), $destination, $posts, $topics);
    	if ($outOfScope === true) {
    		$this->recurciveMoveAddition($original, $destination, $posts, $topics);
    	}

    	$this->container
    		 ->get('cornichon.forum.topic')
    		 ->moveFromBoardToBoard($original, $destination);

    	// $this->changeBoardParent($original, $destination);

    	$this->em->flush();
    }

    private function recurciveMoveNegation(Board $source, Board $destination, $posts, $topics)
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

    private function recurciveMoveAddition(Board $source, Board $destination, $posts, $topics)
    {
    	$destination->getStat()->setPosts(
			$destination->getStat()->getPosts() + $posts
		);
		$destination->getStat()->setTopics(
			$destination->getStat()->getTopics() + $topics
		);
    	
		$this->em->persist($destination->getStat());

    	if ($destination->getParent() !== null) {
    		$this->recurciveMoveAddition($source, $destination->getParent(), $posts, $topics);
    	}
    }
}