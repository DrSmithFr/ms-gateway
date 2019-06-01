<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 * @package App\Repository
 * @codeCoverageIgnore
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAll(): array
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->addSelect('r')
            ->addSelect('g')
            ->addSelect('gr')
            ->from(User::class, 'u')
            ->leftJoin('u.roles', 'r')
            ->leftJoin('u.groups', 'g')
            ->leftJoin('g.roles', 'gr')
            ->getQuery()
            ->getResult();
    }
}
