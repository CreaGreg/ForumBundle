<?php

namespace Cornichon\ForumBundle\Repository;

use Cornichon\ForumBundle\Entity\MessageStarInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MessageStarRepository extends EntityRepository
{

}