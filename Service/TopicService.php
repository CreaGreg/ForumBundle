<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\TopicStat;
use Cornichon\ForumBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

class TopicService extends BaseService {

	protected function createTopicStat()
	{
		return new TopicStat();
	}

	public function getById($topicId)
	{
		return $this->em
					->getRepository($this->topicRepositoryClass)
					->find($topicId);
	}

	public function getLatestTopics($offset, $limit)
	{
		return $this->em->getRepository($this->topicRepositoryClass)->getLatestTopics($offset, $limit);
	}

	public function getLatestTopicsByBoard($board, $offset, $limit)
	{
		return $this->em->getRepository($this->topicRepositoryClass)->getLatestTopicsByBoard($board, $offset, $limit);
	}

	public function getAll()
	{
		return new ArrayCollection($this->em->getRepository($this->topicRepositoryClass)->findAll());
	}

	public function flag(Topic $topic, UserInterface $user)
	{

	}

	public function save(Topic $topic)
	{
		if ($topic->getUser() === null) {
			$topic->setUser(
				$this->container->get('security.context')->getToken()->getUser()
			);
		}

		if ($topic->getSlug() === null) {
			$topic->setSlug();
		}

		if ($topic->getBoard() === null) {
			throw new \Cornichon\ForumBundle\Exception\BoardNotSetException();
		}

		if ($topic->getStat() === null) {
			$topicStat = $this->createTopicStat();
			$topicStat->setTopic($topic);
			$topicStat->setLastUser($topic->getUser());
			$this->em->persist($topicStat);

			$topic->setStat($topicStat);
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