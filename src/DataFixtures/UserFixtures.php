<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use App\Security\Encoder\UserEncoder;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

/**
 * Class UserFixtures
 *
 * @package App\DataFixtures
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_DEV = 'user-dev';
    public const USER_USER = 'user-user';
    public const USER_ADMIN = 'user-admin';

    /**
     * @var UserService
     */
    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws Exception
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $dev   = $this->makeUser('dev', 'dev', [GroupFixtures::GROUP_SUPER_ADMIN]);
        $user  = $this->makeUser('user', 'user', [GroupFixtures::GROUP_USER]);
        $admin = $this->makeUser('admin', 'admin', [GroupFixtures::GROUP_ADMIN]);

        $manager->persist($dev);
        $manager->persist($user);
        $manager->persist($admin);

        $manager->flush();

        $this->addReference(self::USER_DEV, $dev);
        $this->addReference(self::USER_USER, $user);
        $this->addReference(self::USER_ADMIN, $admin);
    }

    /**
     * @throws Exception
     * @param string $pass
     * @param array  $groups
     * @param string $name
     * @return User
     */
    private function makeUser(string $name, string $pass, array $groups = []): User
    {
        $user = $this->service->createUser();
        $user->setEmail($name);

        $this->service->updatePassword($user, $pass);

        foreach ($groups as $key) {
            /** @var Group $group */
            $group = $this->getReference($key);
            $user->addGroup($group);
        }

        return $user;
    }

    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
        ];
    }
}
