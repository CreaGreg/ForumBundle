<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Board;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BoardRepository extends EntityRepository
{
	public function getLatestBoards($offset, $limit)
	{
		$query = $this->createQueryBuilder('b')
					  ->select(array('b', 's'))
					  ->join('b.stat', 's')
					  ->orderBy('b.id', 'DESC')
					  ->getQuery()
					  ->setFirstResult($offset)
					  ->setMaxResults($limit);

		return new Paginator($query, $fetchJoinCollection = true);
	}

}