<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Role;
use App\Enum\SecurityRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class GroupFixtures
 *
 * @package App\DataFixtures
 * @codeCoverageIgnore
 */
class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    public const GROUP_SUPER_ADMIN = 'group-super-admin';
    public const GROUP_ADMIN = 'group-admin';
    public const GROUP_USER = 'group-user';

    public function load(ObjectManager $manager): void
    {
        $superAdmins = $this->makeGroup('super admins', SecurityRoleEnum::SUPER_ADMIN);
        $admins      = $this->makeGroup('admins', SecurityRoleEnum::ADMIN);
        $users       = $this->makeGroup('users', SecurityRoleEnum::USER);

        $manager->persist($superAdmins);
        $manager->persist($admins);
        $manager->persist($users);

        $manager->flush();

        $this->addReference(self::GROUP_SUPER_ADMIN, $superAdmins);
        $this->addReference(self::GROUP_ADMIN, $admins);
        $this->addReference(self::GROUP_USER, $users);
    }

    private function makeGroup(string $name, string $roleReference): Group
    {
        /** @var Role $role */
        $role = $this->getReference($roleReference);

        return (new Group())
            ->setName($name)
            ->addRole($role);
    }

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }
}
