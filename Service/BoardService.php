<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BoardService {

	private $container;
	private $em;

	public function __construct (ContainerInterface $container)
	{
		$this->container = $container;
		$this->em = $container->get('doctrine')->getEntityManager();
	}

	public function save (Board $board)
	{
		if ($board->getUser() === null) {
			throw new \Cornichon\ForumBundle\Exception\UserNotSetException();
		}

		if ($board->getStat() === null) {
			$boardStat = new BoardStat();
			$boardStat->setBoard($board);
			$this->em->persist($boardStat);
		}

		if ($board->getId() === null) {
			$board->setDateCreated(new \DateTime());
		}
		else {
			$board->getDateModified(new \DateTime());
		}

		$this->em->persist($board);
		$this->em->flush($board);

		return $board;
	}

}