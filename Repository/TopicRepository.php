<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\TopicStat;
use Cornichon\ForumBundle\Entity\Message;

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

	public function moveFromBoardToBoard(Board $from, Board $to)
	{
		return $this->createQueryBuilder('t')
			->update('t')
            ->set('t.board', $to)
            ->where('t.board = :board')->setParameter('board', $from)
            ->getQuery()
            ->execute();
	}

}