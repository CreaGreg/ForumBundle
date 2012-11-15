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

    /**
     * Get all topics in board
     * Performance can be poor because it will gather all data
     * 
     * @param  Board  $board
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getInBoard(Board $board)
    {
        $query = $this->createQueryBuilder('t')
                      ->select(array('t', 'u', 's', 'l'))
                      ->join('t.user', 'u')
                      ->join('t.stat', 's')
                      ->join('s.lastUser', 'l')
                      ->where('t.board = :board')->setParameter('board', $board)
                      ->getQuery();

        return new Paginator($query, false);
    }

    /**
     * Get the latest topics in general
     * 
     * @param  integer  $offset
     * @param  itenger  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
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

    /**
     * Get the latest topics by board ids
     * 
     * @param  array    $boardIds
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByBoardIds($boardIds, $offset, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('t')
                      ->select(array('t', 'u', 's', 'l'))
                      ->join('t.user', 'u')
                      ->join('t.stat', 's')
                      ->join('s.lastUser', 'l')
                      ->orderBy('t.dateCreated', 'DESC');

        $queryBuilder->where($queryBuilder->expr()->in('t.board', $boardIds));

        $query = $queryBuilder->getQuery()
                      ->setFirstResult($offset)
                      ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    /**
     * Get the latest topics by board
     * 
     * @param  Board    $board
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByBoard(Board $board, $offset, $limit)
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

    /**
     * Move all topics from a board to another board
     * 
     * @param  Board   $from
     * @param  Board   $to
     * @param  boolean $flush = true
     * 
     * @return integer 
     */
    public function moveFromBoardToBoard(Board $from, Board $to)
    {
        return $this->createQueryBuilder('t')
                    ->update($this->getEntityName(), 't')
                    ->set('t.board', $to->getId())
                    ->where('t.board = :board')->setParameter('board', $from->getId())
                    ->getQuery()
                    ->execute();
    }

}