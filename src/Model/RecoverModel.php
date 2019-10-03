<?php

declare(strict_types=1);

namespace App\Model;

class RecoverModel
{
    /**
     * @var string|null
     */
    private $token;

    /**
     * @var string|null
     */
    private $passphrase;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return self
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }

    /**
     * @param string|null $passphrase
     * @return self
     */
    public function setPassphrase(?string $passphrase): self
    {
        $this->passphrase = $passphrase;
        return $this;
    }
}
