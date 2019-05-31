<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Role;
use App\Enum\SecurityRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RoleFixtures
 *
 * @package App\DataFixtures
 * @codeCoverageIgnore
 */
class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $superAdmin = (new Role())
            ->setName(SecurityRoleEnum::SUPER_ADMIN);

        $admin = (new Role())
            ->setName(SecurityRoleEnum::ADMIN);

        $user = (new Role())
            ->setName(SecurityRoleEnum::USER);

        $manager->persist($superAdmin);
        $manager->persist($admin);
        $manager->persist($user);

        $manager->flush();

        $this->addReference(SecurityRoleEnum::SUPER_ADMIN, $superAdmin);
        $this->addReference(SecurityRoleEnum::ADMIN, $admin);
        $this->addReference(SecurityRoleEnum::USER, $user);
    }
}
