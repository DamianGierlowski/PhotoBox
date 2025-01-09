<?php

namespace App\Repository;

use App\Entity\Commission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class CommissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commission::class);
    }

   public function findAllForUser(UserInterface $user): array
   {
        return $this->findBy(['user' => $user]);
   }

   public function findOneByGuidForUser(string $guid, UserInterface $user): ?Commission
   {
       return $this->findOneBy(['guid' => $guid, 'user' => $user]);
   }
}
