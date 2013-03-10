<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\Flag;
use Cornichon\ForumBundle\Entity\Moderation;

use Doctrine\Common\Collections\ArrayCollection;

class FlagService extends BaseService
{

    protected function createFlag()
    {
        return new Flag();
    }

}