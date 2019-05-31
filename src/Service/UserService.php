<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Security\Encoder\UserEncoder;
use Exception;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @var UserEncoder
     */
    private $encoder;

    public function __construct(UserEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @throws Exception
     * @return User
     */
    public function createUser(): User
    {
        $user = new User();

        $salt = $this->encoder->generateSalt();
        $uuid = Uuid::uuid4();

        $user
            ->setSalt($salt)
            ->setExternalId($uuid);

        return $user;
    }

    public function updatePassword(User $user, string $pass): void
    {
        $password = $this->encoder->encodePassword($user, $pass);
        $user->setPassword($password);
    }
}
