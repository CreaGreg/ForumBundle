<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\BoardStat;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BoardRepository extends EntityRepository
{

    /**
     * Get a Board entity
     * 
     * @param  integer  $boardId
     * @param  boolean  $deleted = false
     * 
     * @return Board|null
     */
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

    /**
     * Gets all boards and build up the full hierarchie
     * 
     * @param  boolean $deleted = false
     * 
     * @return ArrayCollection
     */
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

    /**
     * Get the main boards
     * 
     * @param  boolean $deleted = false
     * 
     * @return ArrayCollection
     */
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

    /**
     * Get a list of boards by parent board
     * 
     * @param  Board  $board
     * 
     * @return ArrayCollection
     */
    public function getBoardsByParentBoard(Board $board)
    {
        $queryBuilder = $this->createQueryBuilder('b')
                      ->select(array('b', 's'))
                      ->where('b.parent = :parent')->setParameter('parent', $board->getId())
                      ->join('b.stat', 's');

        $query = $queryBuilder->getQuery();

        return new ArrayCollection($query->getResult());   
    }

    /**
     * Get a list of boards by parent boards
     * 
     * @param  ArrayCollection $boards
     * 
     * @return ArrayCollection
     */
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

    /**
     * Get all board IDs
     *
     * @param  boolean $deleted = false
     * @return array
     */
    public function getBoardIdsRaw($deleted = false)
    {
        $connection = $this->getEntityManager()->getConnection();

        return $connection->fetchAll("SELECT id, parent_id FROM board");
    }

    /**
     * Get the latest boards based on the board id
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
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

    /**
     * Get the popular boards based on the number of posts
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
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

    /**
     * Change the parent board of all boards that has $original as a parent
     * 
     * @param  Board  $source 
     * @param  Board  $destination 
     * 
     * @return integer
     */
    public function switchBoardParent(Board $source, Board $destination)
    {
        return $this->createQueryBuilder('b')
            ->update($this->getEntityName(), 'b')
            ->set('b.parent', $destination->getId())
            ->where('b.parent = :source')->setParameter('source', $source->getId())
            ->getQuery()
            ->execute();
    }

}