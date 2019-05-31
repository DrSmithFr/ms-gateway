<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Entity\User;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @throws Exception
     * @return User
     */
    public function createUser(): User
    {
        $user = new User();

        $salt = $this->generateSalt();
        $uuid = Uuid::uuid4();

        $user
            ->setSalt($salt)
            ->setExternalId($uuid);

        return $user;
    }

    public function updatePassword(User $user, string $pass): void
    {
        $password = $this->encodePassword($user, $pass);
        $user->setPassword($password);
    }

    private function generateSalt(): string
    {
        return uniqid(sprintf('%s', mt_rand()), true);
    }

    private function encodePassword(User $user, string $pass): string
    {
        if ($user->getSalt()) {
            return password_hash($pass, PASSWORD_BCRYPT, ['salt' => $user->getSalt()]);
        }

        return password_hash($pass, PASSWORD_BCRYPT);
    }
}
