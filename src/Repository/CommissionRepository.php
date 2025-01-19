<?php

namespace App\Repository;

use App\Entity\Commission;
use App\Enum\CommissionStatusEnum;
use DateTime;
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

   public function getTotalActiveCommissionsForUser(UserInterface $user): int
   {
       $queryBuilder = $this->createQueryBuilder('c')
           ->select('count(c.id)')
           ->where('c.status <> :status')
           ->andWhere('c.user = :user')
           ->setParameter('status', CommissionStatusEnum::Completed->value)
           ->setParameter('user', $user);

       return (int) $queryBuilder->getQuery()->getSingleScalarResult();
   }

   public function findRecentCommissionsForUser(UserInterface $user, int $limit = 5): array
   {
       return $this->createQueryBuilder('c')
            ->where('c.user = :user')
           ->andWhere('c.date < :today')
            ->setParameter('user', $user)
           ->setParameter('today', new \DateTime())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
   }

    public function findIncomingCommissionsForUser(UserInterface $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.date >= :today')
            ->setParameter('user', $user)
            ->setParameter('today', new \DateTime())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findCommissionByTitleAndDateForUser(UserInterface $user, ?string $title = null, ?string $date = null)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $user);

        if (!empty($title)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('c.title', ':title'))
                ->setParameter('title', "%$title%");
        }
        if (!empty($date)) {
            $queryBuilder->andWhere('c.date = :date')
                ->setParameter('date', $date);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
