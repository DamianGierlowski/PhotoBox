<?php

namespace App\Security\Voter;

use App\Entity\Gallery;
use App\UniqueNameInterface\PermissionInterface;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GalleryVoter extends Voter
{

    private const SUPPORTED_ATTRIBUTES = [
        PermissionInterface::OWNER
    ];

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, self::SUPPORTED_ATTRIBUTES) && $subject instanceof Gallery;
    }

    #[Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            PermissionInterface::OWNER => $this->voteOnOwner($subject, $user),
            default => false,
        };
    }

    protected function voteOnOwner(Gallery $subject, UserInterface $user): bool
    {
        if ($subject->getCommission()->getUser() === $user) {
            return true;
        }

        return false;
    }
}
