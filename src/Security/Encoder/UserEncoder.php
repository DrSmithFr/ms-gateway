<?php

declare(strict_types=1);

namespace App\Security\Encoder;

use App\Entity\User;

class UserEncoder
{
    public function generateSalt(): string
    {
        return uniqid('sylly-salt-', false);
    }

    /**
     * TODO use a real encryption
     *
     * @param User   $user
     * @param string $pass
     * @return string
     */
    public function encodePassword(User $user, string $pass): string
    {
        if ($user->getSalt()) {
            return sha1($pass . $user->getSalt());
        }

        return sha1($pass);
    }
}
