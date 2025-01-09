<?php

namespace App\Security;

use App\Entity\GuestUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class GuestUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Dynamically create a GuestUser using the provided identifier (email)
        return new GuestUser($identifier);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof GuestUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        // Since GuestUser is stateless, we can return it as-is
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === GuestUser::class;
    }
}