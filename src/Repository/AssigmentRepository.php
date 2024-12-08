<?php

namespace App\Repository;

use App\Entity\Assigment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class AssigmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assigment::class);
    }

   public function findAllForUser(UserInterface $user): array
   {
        return $this->findBy(['user' => $user]);
   }

   public function findOneByGuidForUser(string $guid, UserInterface $user): ?Assigment
   {
       return $this->findOneBy(['guid' => $guid, 'user' => $user]);
   }
}
