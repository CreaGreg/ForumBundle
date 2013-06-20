<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\Topic;

use Cornichon\ForumBundle\Entity\MessageInterface;
use Cornichon\ForumBundle\Entity\TopicInterface;

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
     * @return MessageInterface
     */
    public function find($id, $isDeleted = false)
    {
        $queryBuilder = $this->createQueryBuilder('m')
                             ->select(array('m'))
                             // ->join('m.user', 'u')
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
     * @param  TopicInterface  $topic
     * @param  integer         $offset
     * @param  integer         $limit
     * @param  boolean         $isDeleted
     * 
     * @return Paginator
     */
    public function getMessagesByTopic(TopicInterface $topic, $offset, $limit, $isDeleted)
    {
        $queryBuilder = $this->createQueryBuilder('m')
                             ->select(array('m'))
                             // ->join('m.user', 'u')
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
     * @param  TopicInterface  $topic
     * 
     * @return Message|null
     */
    public function getTopicBodyByTopic(TopicInterface $topic)
    {
        $queryBuilder = $this->createQueryBuilder('m')
                             ->select(array('m'))
                             // ->join('m.user', 'u')
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