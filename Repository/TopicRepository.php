<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Board;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TopicRepository extends EntityRepository
{
	public function getLatestTopics($offset, $limit)
	{
		$query = $this->createQueryBuilder('t')
					  ->select(array('t', 's'))
					  ->join('t.stat', 's')
					  ->orderBy('t.id', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, $fetchJoinCollection = true);
	}

	public function getLatestTopicsByBoard(Board $board, $offset, $limit)
	{
		$query = $this->createQueryBuilder('t')
					  ->select(array('t', 's'))
					  ->join('t.stat', 's')
					  ->where('t.board = :board')->setParameter('board', $board)
					  ->orderBy('t.id', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, $fetchJoinCollection = true);
	}

}