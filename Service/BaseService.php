<?php

namespace Cornichon\ForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseService {

	protected $container;
	protected $em;

	protected $boardRepositoryClass;
	protected $topicRepositoryClass;
	protected $messageRepositoryClass;

	public function __construct (ContainerInterface $container)
	{
		$this->container = $container;
		$this->em = $container->get('doctrine')->getEntityManager();

		$this->boardRepositoryClass = $this->container->getParameter('cornichon_forum.board_repository.class');
		$this->topicRepositoryClass = $this->container->getParameter('cornichon_forum.topic_repository.class');
		$this->messageRepositoryClass = $this->container->getParameter('cornichon_forum.message_repository.class');
	}


}