<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\Topic;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MessageRepository extends EntityRepository
{
    /**
     * Get a message by id
     * 
     * @param  integer  $id
     * @param  boolean  $deleted
     * 
     * @return Message
     */
    public function find($id, $isDeleted = false)
    {
        $queryBuilder = $this->createQueryBuilder('m')
                             ->select(array('m','u'))
                             ->join('m.user', 'u')
                             ->where('m.id = :id')->setParameter('id', $id);

        if ($isDeleted !== null) {
            $queryBuilder->andWhere('m.isDeleted = :isDeleted')->setParameter('isDeleted', $isDeleted);
        }
        
        $query = $queryBuilder->getQuery();

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            return null;
        }
    }

    /**
     * Get a collection of messages
     * 
     * @param  Topic    $topic
     * @param  integer  $offset
     * @param  integer  $limit
     * @param  boolean  $isDeleted
     * 
     * @return Paginator
     */
    public function getMessagesByTopic(Topic $topic, $offset, $limit, $isDeleted)
    {
        $queryBuilder = $this->createQueryBuilder('m')
                             ->select(array('m','u'))
                             ->join('m.user', 'u')
                             ->where('m.topic = :topic')->setParameter('topic', $topic);

        if ($isDeleted !== null) {
            $queryBuilder->andWhere('m.isDeleted = :isDeleted')->setParameter('isDeleted', $isDeleted);
        }
                      
        $query = $queryBuilder->getQuery();
        
        $query->setFirstResult($offset)
              ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    /**
     * Get a topic body by topic
     * 
     * @param  Topic  $topic
     * 
     * @return Message|null
     */
    public function getTopicBodyByTopic(Topic $topic)
    {
        $queryBuilder = $this->createQueryBuilder('m')
                             ->select(array('m','u'))
                             ->join('m.user', 'u')
                             ->where('m.topic = :topic')->setParameter('topic', $topic)
                             ->andWhere('m.isTopicBody = 1');
        
        $query = $queryBuilder->getQuery();

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            return null;
        }
    }
}