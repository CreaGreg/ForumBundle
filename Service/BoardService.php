<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

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

	public function getBoards($offset, $limit)
	{
		return $this->em
		            ->getRepository($this->boardRepositoryClass)
		            ->getBoards($offset, $limit);
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
		}
		$this->em->flush();

		return $board;
	}
}