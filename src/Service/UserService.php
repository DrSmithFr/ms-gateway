<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Entity\User;
use Ramsey\Uuid\Uuid;
use App\Model\ConnectionModel;

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
            ->setUuid($uuid);

        return $user;
    }

    /**
     * @param User   $user
     * @param string $password
     * @return User
     */
    public function updatePassword(User $user, string $password): User
    {
        return $user
            ->setPassword($this->encodePassword($user, $password))
            ->setPlainPassword(null);
    }

    /**
     * @return string
     */
    private function generateSalt(): string
    {
        return uniqid(sprintf('%s', mt_rand()), true);
    }

    /**
     * @param User   $user
     * @param string $pass
     * @return string
     */
    private function encodePassword(User $user, string $pass): string
    {
        if ($user->getSalt()) {
            return password_hash($pass, PASSWORD_ARGON2I, ['salt' => $user->getSalt()]);
        }

        return password_hash($pass, PASSWORD_ARGON2I);
    }

    /**
     * @param User            $user
     * @param ConnectionModel $co
     * @return bool
     */
    public function checkPasswordCorrectness(User $user, ConnectionModel $co): bool
    {
        if ($user->getUuid() !== $co->getUuid()) {
            return false;
        }

        if ($user->getPassword() !== $this->encodePassword($user, $co->getPassword())) {
            return false;
        }

        return true;
    }
}
