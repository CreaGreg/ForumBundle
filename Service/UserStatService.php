<?php

namespace Cornichon\ForumBundle\Service;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Entity\TopicStat;
use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Entity\UserStat;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

class UserStatService extends BaseService
{
    protected function createUserStat()
    {
        return new UserStat();
    }

    /**
     * Create UserStat entity
     * 
     * @param  UserInterface $user
     * @param  boolean       $flush = true
     * 
     * @return UserStat
     */
    public function createUserStatFromUser(UserInterface $user, $flush = true)
    {
        $userStat = $this->createUserStat();

        $userStat->setUser($user);

        $this->em->persist($userStat);

        if ($flush === true) {
            $this->em->flush();
        }

        return $userStat;
    }

    /**
     * Get a UserStat entity from a User and create one if the entity doesn't exist
     * 
     * @param  UserInterface $user
     * @param  boolean       $flush = true
     * 
     * @return UserStat
     */
    public function getByUserOrCreateOne(UserInterface $user, $flush = true)
    {
        // Get the user stat
        $userStat = $this->getByUser($user);

        // If null, we need to create a UserStat entity
        if ($userStat === null) {
            $userStat = $this->createUserStatFromUser($user, $flush);
        }

        return $userStat;
    }

    /**
     * Get a UserStat entity from a User
     * 
     * @param  UserInterface $user
     * 
     * @return UserStat|null
     */
    public function getByUser(UserInterface $user)
    {
        return $this->em
                    ->getRepository($this->userStatRepositoryClass)
                    ->getByUser($user);
    }

    /**
     * Get top users
     * 
     * @param  integer $limit = 10
     * 
     * @return ArrayCollection
     */
    public function getTopUsers($limit = 10)
    {
        return $this->em
                    ->getRepository($this->userStatRepositoryClass)
                    ->getTopUsers($limit);
    }
}