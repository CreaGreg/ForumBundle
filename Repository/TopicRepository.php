<?php

namespace Cornichon\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TopicRepository extends EntityRepository
{
	public function getLatestTopics($offset, $limit)
	{
		$query = $this->createQueryBuilder('t')
					  ->select(array('t', 'u', 's', 'l'))
					  ->join('t.user', 'u')
					  ->join('t.stat', 's')
					  ->join('s.lastUser', 'l')
					  ->orderBy('t.dateCreated', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, false);
	}

	public function getLatestTopicsByBoard($board, $offset, $limit)
	{
		$query = $this->createQueryBuilder('t')
					  ->select(array('t', 'u', 's', 'l'))
					  ->join('t.user', 'u')
					  ->join('t.stat', 's')
					  ->join('s.lastUser', 'l')
					  ->where('t.board = :board')->setParameter('board', $board)
					  ->orderBy('t.dateCreated', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, false);
	}

}