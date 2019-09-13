<?php

declare(strict_types=1);

namespace App\Model;

use Ramsey\Uuid\Uuid;

class ConnectionModel
{
    /**
     * @var Uuid|null
     */
    private $uuid;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @return Uuid|null
     */
    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    /**
     * @param Uuid|null $uuid
     * @return self
     */
    public function setUuid(?Uuid $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

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
}
