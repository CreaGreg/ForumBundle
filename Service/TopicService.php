<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\TopicStat;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TopicService {

	private $container;
	private $em;

	public function __construct (ContainerInterface $container)
	{
		$this->container = $container;
		$this->em = $container->get('doctrine')->getEntityManager();
	}

	public function save (Topic $topic)
	{
		if ($topic->getUser() === null) {
			throw new \Cornichon\ForumBundle\Exception\UserNotSetException();
		}

		if ($message->getBoard() === null) {
			throw new \Cornichon\ForumBundle\Exception\BoardNotSetException();
		}

		if ($topic->getStat() === null) {
			$topicStat = new TopicStat();
			$topicStat->setTopic($topic);
			$topicStat->setLastUser($topic->getUser());
			$this->em->persist($topicStat);
		}

		if ($topic->getId() === null) {
			$topic->setDateCreated(new \DateTime());
		}
		else {
			$topic->getDateModified(new \DateTime());
		}

		$this->em->persist($topic);
		$this->em->flush($topic);

		return $topic;
	}

}