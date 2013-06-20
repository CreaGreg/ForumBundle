<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;

use Cornichon\ForumBundle\Entity\BoardInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TopicRepository extends EntityRepository
{

    /**
     * Get all topics in board
     * Performance can be poor because it will gather all data
     * 
     * @param  BoardInterface  $board
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getInBoard(BoardInterface $board)
    {
        $query = $this->createQueryBuilder('t')
                      ->select(array('t', 'l'))
                      ->join('t.user', 'u')
                      // ->join('t.lastUser', 'l')
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
                      ->select(array('t'))
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
                      ->select(array('t'))
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
     * @param  BoardInterface   $board
     * @param  integer          $offset
     * @param  integer          $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByBoard(BoardInterface $board, $offset, $limit)
    {
        $query = $this->createQueryBuilder('t')
                      ->select(array('t'))
                      ->where('t.board = :board')->setParameter('board', $board)
                      ->orderBy('t.dateCreated', 'DESC')
                      ->getQuery()
                      ->setFirstResult($offset)
                      ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    /**
     * Get the latest topics by user
     * 
     * @param  UserInterface    $user
     * @param  integer          $offset
     * @param  integer          $limit
     * 
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getLatestTopicsByUser(UserInterface $user, $offset, $limit)
    {
        $query = $this->createQueryBuilder('t')
                      ->select(array('t'))
                      ->where('t.user = :user')->setParameter('user', $user)
                      ->orderBy('t.dateCreated', 'DESC')
                      ->getQuery()
                      ->setFirstResult($offset)
                      ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    /**
     * Move all topics from a board to another board
     * 
     * @param  BoardInterface   $from
     * @param  BoardInterface   $to
     * @param  boolean          $flush = true
     * 
     * @return integer 
     */
    public function moveFromBoardToBoard(BoardInterface $from, BoardInterface $to)
    {
        return $this->createQueryBuilder('t')
                    ->update($this->getEntityName(), 't')
                    ->set('t.board', $to->getId())
                    ->where('t.board = :board')->setParameter('board', $from->getId())
                    ->getQuery()
                    ->execute();
    }

}