<?php

namespace Cornichon\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ModerationRepository extends EntityRepository
{
    /**
     * Get the latest moderation actions
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return Paginator
     */
    public function getLatestModerations($offset, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('mo')
                             ->select(array('mo', 'u', 'm', 't', 'b'))
                             ->join('mo.user', 'u')
                             ->leftJoin('mo.message', 'm')
                             ->leftJoin('mo.topic', 't')
                             ->leftJoin('mo.board', 'b')
                             ->orderBy('mo.id', 'DESC');
                      
        $query = $queryBuilder->getQuery();
        
        $query->setFirstResult($offset)
              ->setMaxResults($limit);

        return new Paginator($query, false);
    }
}
