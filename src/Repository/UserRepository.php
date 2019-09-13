<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\NonUniqueResultException;
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

    /**
     * @return array|User[]
     */
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

    /**
     * @throws NonUniqueResultException
     * @param Uuid $uuid
     * @return User|null
     */
    public function findOneByUuid(Uuid $uuid): ?User
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
