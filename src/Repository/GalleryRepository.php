<?php

namespace App\Repository;

use App\Entity\Gallery;
use App\Enum\CommissionStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Gallery>
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function findOneByGuid(string $guid): ?Gallery
    {
        return $this->findOneBy(['guid' => $guid]);
    }

    public function getTotalActiveGalleryForUser(UserInterface $user): int
    {
        $queryBuilder = $this->createQueryBuilder('g')
            ->select('count(g.id)')
            ->join('g.createdBy', 'u')
            ->where('g.deleted = :deleted')
            ->andWhere('u.email = :userEmail')
            ->setParameter('deleted', false)
            ->setParameter('userEmail', $user->getUserIdentifier());

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
