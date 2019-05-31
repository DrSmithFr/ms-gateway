<?php

declare(strict_types=1);

namespace App\Security\Encoder;

use App\Entity\User;

class UserEncoder
{
    public function generateSalt(): string
    {
        return uniqid(sprintf('%s', mt_rand()), true);
    }

    /**
     * @param User   $user
     * @param string $pass
     * @return string
     */
    public function encodePassword(User $user, string $pass): string
    {
        if ($user->getSalt()) {
            return password_hash($pass, PASSWORD_BCRYPT, ['salt' => $user->getSalt()]);
        }

        return password_hash($pass, PASSWORD_BCRYPT);
    }
}
