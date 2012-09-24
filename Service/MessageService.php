<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\Security\Core\User\UserInterface;

class MessageService extends BaseService
{

	public function getMessagesByTopic($topic, $offset, $limit)
	{
		return $this->em->getRepository($this->messageRepositoryClass)->getMessagesByTopic($topic, $offset, $limit);
	}

	public function flag(Message $message, UserInterface $user)
	{

	}

	public function save(Message $message, $isTopic = false)
	{
		if ($message->getUser() === null) {
			$message->setUser(
				$this->container->get('security.context')->getToken()->getUser()
			);
		}

		if ($message->getTopic() === null) {
			throw new \Cornichon\ForumBundle\Exception\TopicNotSetException();
		}

		/**
		 * Only increment topic stat when a user adds a response to a topic
		 */
		if ($isTopic === false) {
			$message->getTopic()->getStat()->setPosts(
				$message->getTopic()->getStat()->getPosts() + 1
			);
		}

		/**
		 * Update the last message of topic
		 */
		$message->getTopic()->getStat()->setLastMessageDate(new \DateTime());

		if ($message->getId() === null) {
			$message->setDateCreated(new \DateTime());

			if ($isTopic === false) {
				/**
				 * Increment board message count when creating a new message
				 */
				$this->container
					 ->get('cornichon.forum.board')
					 ->incrementStatPosts($message->getTopic()->getBoard());
			}
			else {
				/**
				 * Increment board topic count when creating a new topic
				 */
				$this->container
					 ->get('cornichon.forum.board')
					 ->incrementStatTopics($message->getTopic()->getBoard());
			}
		}
		else {
			$message->getDateModified(new \DateTime());
		}

		$this->em->persist($message);
		$this->em->flush();

		return $message;
	}

}