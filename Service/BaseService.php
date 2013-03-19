<?php

namespace Cornichon\ForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseService
{
	protected $container;
	protected $em;

	protected $boardRepositoryClass;
	protected $topicRepositoryClass;
	protected $messageRepositoryClass;
	protected $userStatRepositoryClass;

	public function __construct (ContainerInterface $container)
	{
		$this->container = $container;
		$this->em = $container->get('doctrine')->getEntityManager();

		$this->boardRepositoryClass = $this->container->getParameter('cornichon_forum.board_repository.class');
		$this->flagRepositoryClass = $this->container->getParameter('cornichon_forum.flag_repository.class');
		$this->topicRepositoryClass = $this->container->getParameter('cornichon_forum.topic_repository.class');
		$this->messageRepositoryClass = $this->container->getParameter('cornichon_forum.message_repository.class');
		$this->moderationRepositoryClass = $this->container->getParameter('cornichon_forum.moderation_repository.class');
		$this->userStatRepositoryClass = $this->container->getParameter('cornichon_forum.user_stat_repository.class');
	}


}