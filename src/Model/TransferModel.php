<?php

declare(strict_types=1);

namespace App\Model;

class TransferModel
{
    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $passphrase;

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
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
     */
    public function setPassphrase(?string $passphrase): void
    {
        $this->passphrase = $passphrase;
    }
}
