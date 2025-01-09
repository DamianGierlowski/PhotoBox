<?php

namespace App\Entity;

use Override;
use Symfony\Component\Security\Core\User\UserInterface;

class GuestUser implements UserInterface
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
    #[Override] public function getRoles(): array
    {
        return ['ROLE_GUEST'];
    }

    #[Override]
    public function eraseCredentials(): void
    {

    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

}