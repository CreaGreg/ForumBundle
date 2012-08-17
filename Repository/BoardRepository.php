<?php

namespace Cornichon\ForumBundle\Repository;

use Loverz\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BoardRepository extends EntityRepository
{

	public function getBoards($offset, $limit)
	{
		$query = $this->createQueryBuilder('b')
					  ->select(array('b', 's'))
					  ->join('b.stat', 's')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, false);
	}

	public function getLatestBoards($offset, $limit)
	{
		$query = $this->createQueryBuilder('b')
					  ->select(array('b', 's'))
					  ->join('b.stat', 's')
					  ->orderBy('b.id', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, false);
	}

	public function getPopularBoards($offset, $limit)
	{
		$query = $this->createQueryBuilder('b')
					  ->select(array('b', 's'))
					  ->join('b.stat', 's')
					  ->orderBy('s.posts', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, false);
	}

}