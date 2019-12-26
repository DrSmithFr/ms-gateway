<?php

declare(strict_types=1);

namespace App\Model;

class GpgModel
{
    /**
     * @var string|null
     */
    private $key;

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string|null $key
     *
     * @return self
     */
    public function setKey(?string $key): self
    {
        $this->key = $key;
        return $this;
    }
}
