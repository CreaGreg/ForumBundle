<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BoardRepository extends EntityRepository
{
    public function find($id, $deleted = false)
    {
        $queryBuilder = $this->createQueryBuilder('b')
                             ->select(array('b', 's'))
                             ->join('b.stat', 's')
                             ->where('b.id = :id')->setParameter('id', $id);

        if ($deleted !== null) {
            $queryBuilder->andWhere('b.isDeleted = :isDeleted')
                         ->setParameter('isDeleted', $deleted);
        }
                      
        $query = $queryBuilder->getQuery();

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            return null;
        }
    }

    public function getBoards($deleted = false)
    {
        $queryBuilder = $this->createQueryBuilder('b')
                             ->select(array('b', 's'))
                             ->join('b.stat', 's');

        if ($deleted !== null) {
            $queryBuilder->andWhere('b.isDeleted = :isDeleted')
                         ->setParameter('isDeleted', $deleted);
        }
        
        $query = $queryBuilder->getQuery();

        return new Paginator($query, false);
    }

    public function getMainBoards($deleted = false)
    {
        $queryBuilder = $this->createQueryBuilder('b')
                             ->select(array('b', 's'))
                             ->where('b.parent IS NULL')
                             ->join('b.stat', 's');

        if ($deleted !== null) {
            $queryBuilder->andWhere('b.isDeleted = :isDeleted')
                         ->setParameter('isDeleted', $deleted);
        }
        
        $query = $queryBuilder->getQuery();

        return new ArrayCollection($query->getResult());
    }

    public function getBoardsByParentBoards(ArrayCollection $boards)
    {
        $boardIds = array();
        foreach ($boards as $board) {
            $boardIds[] = $board->getId();
        }

        $queryBuilder = $this->createQueryBuilder('b')
                      ->select(array('b', 's'));
        $queryBuilder->where($queryBuilder->expr()->in('b.parent', $boardIds))
                      ->join('b.stat', 's');

        $query = $queryBuilder->getQuery();

        return new ArrayCollection($query->getResult());
    }

    public function getBoardIdsRaw($deleted = false)
    {
        $connection = $this->getEntityManager()->getConnection();

        return $connection->fetchAll("SELECT id, parent_id FROM board");
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
                      ->where('b.parent IS NULL')
                      ->orderBy('s.posts', 'DESC')
                      ->getQuery()
                      ->setFirstResult($offset)
                      ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function changeBoardParent(Board $source, Board $destination)
    {
        return $this->createQueryBuilder('b')
            ->update('b')
            ->set('b.parent', $destination)
            ->where('b.parent = :source')->setParameter('source', $source)
            ->getQuery()
            ->execute();
    }

}