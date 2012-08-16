<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

class MessageService extends BaseService
{

	public function getMessagesByTopic($topic, $offset, $limit)
	{
		return $this->em->getRepository($this->messageRepositoryClass)->getMessagesByTopic($topic, $offset, $limit);
	}

	public function save (Message $message, $isTopic = false)
	{
		if ($message->getUser() === null) {
			$message->setUser(
				$this->container->get('security.context')->getToken()->getUser()
			);
		}

		if ($message->getTopic() === null) {
			throw new \Cornichon\ForumBundle\Exception\TopicNotSetException();
		}

		if ($isTopic === false) {
			$message->getTopic()->getStat()->setPosts(
				$message->getTopic()->getStat()->getPosts() + 1
			);
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