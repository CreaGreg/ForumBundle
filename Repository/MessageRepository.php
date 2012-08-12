<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Topic;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MessageRepository extends EntityRepository
{
	public function getLatestMessagesByTopic(Topic $topic, $offset, $limit)
	{
		$query = $this->createQueryBuilder('m')
					  ->select(array('m','u'))
					  ->join('m.user', 'u')
					  ->where('m.topic = :topic')->setParameter('topic', $topic)
					  ->orderBy('m.id', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, $fetchJoinCollection = true);
	}
}