<?php

namespace App\Service;

use App\Entity\User;
use App\Util\GuidFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function createUser(
        string $email,
        string $name,
        string $surname,
        string $plainPassword,
        array $roles = ['ROLE_USER']
    ): User
    {
        $user = new User();
        $user->setEmail($email)
            ->setName($name)
            ->setSurname($surname)
            ->setPassword($this->passwordHasher->hashPassword($user ,$plainPassword))
            ->setRoles($roles)
            ->setGuid(GuidFactory::generate());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}