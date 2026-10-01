<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :username')
            ->orWhere('u.name = :username')
            ->setParameter('username', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}