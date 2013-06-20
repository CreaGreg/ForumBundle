<?php

namespace Cornichon\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class FlagRepository extends EntityRepository
{
    /**
     * Get the latest moderation actions
     * 
     * @param  integer  $offset
     * @param  integer  $limit
     * 
     * @return Paginator
     */
    public function getLatestFlags($offset, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('f')
                             ->select(array('f', 'mo', 'u', 'm', 't'))
                             ->leftJoin('f.moderation', 'mo')
                             ->join('f.users', 'u')
                             ->leftJoin('f.message', 'm')
                             ->leftJoin('f.topic', 't')
                             ->orderBy('f.id', 'DESC');
                      
        $query = $queryBuilder->getQuery();
        
        $query->setFirstResult($offset)
              ->setMaxResults($limit);

        return new Paginator($query, false);
    }
}
