<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Overview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class OverviewRepository
 * @package App\Repository
 * @codeCoverageIgnore
 */
class OverviewRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Overview::class);
    }

    /**
     * @param User $user
     * @return array
     */
    public function findAllForUser(User $user): array
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
