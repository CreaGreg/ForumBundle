<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageService {

	private $container;
	private $em;

	public function __construct (ContainerInterface $container)
	{
		$this->container = $container;
		$this->em = $container->get('doctrine')->getEntityManager();
	}

	public function save (Message $message)
	{
		if ($message->getUser() === null) {
			throw new \Cornichon\ForumBundle\Exception\UserNotSetException();
		}

		if ($message->getTopic() === null) {
			throw new \Cornichon\ForumBundle\Exception\TopicNotSetException();
		}

		if ($message->getId() === null) {
			$message->setDateCreated(new \DateTime());
		}
		else {
			$message->getDateModified(new \DateTime());
		}

		$this->em->persist($message);
		$this->em->flush();

		return $message;
	}

}