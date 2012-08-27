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

	public function getBoards()
	{
		/**
		 * Get all the board id and their parent ids
		 */
		$ids = $this->em
					->getRepository($this->boardRepositoryClass)
					->getBoardIdsRaw();

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
		            ->getMainBoards();

		/**
		 * Get all the boards
		 */
		$boards = $this->em
			            ->getRepository($this->boardRepositoryClass)
			            ->getBoards();
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

		if ($board->getSlug() === null) {
			$board->setSlug();
		}

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
}