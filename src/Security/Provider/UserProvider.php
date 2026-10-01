<?php

namespace App\Security\Provider;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserLoaderInterface $userLoader)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new \Exception('Not implemented');
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->userLoader->loadUserByIdentifier($identifier);
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}