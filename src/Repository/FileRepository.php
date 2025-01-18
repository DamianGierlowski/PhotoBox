<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findOneByGuid(string $guid): ?File
    {
        return $this->findOneBy(['guid' => $guid]);
    }

    public function getTotalSize(UserInterface $user): int
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->select('SUM(f.size) / 1000 / 1024 ')
            ->join('f.createdBy', 'u')
            ->where('f.deleted = :deleted')
            ->andWhere('u.email = :userEmail')
            ->setParameter('deleted', false)
            ->setParameter('userEmail', $user->getUserIdentifier());

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

}
