<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Authorization
 *
 * @package App\Entity
 * @ORM\Embeddable()
 */
class Authorization
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $readable;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $writable;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $executable;

    /**
     * Authorization constructor.
     *
     * @param bool $readable
     * @param bool $writable
     * @param bool $executable
     */
    public function __construct(
        bool $readable = false,
        bool $writable = false,
        bool $executable = false
    ) {
        $this->readable   = $readable;
        $this->writable   = $writable;
        $this->executable = $executable;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * @param bool $readable
     * @return $this
     */
    public function setReadable(bool $readable): self
    {
        $this->readable = $readable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * @param bool $writable
     * @return $this
     */
    public function setWritable(bool $writable): self
    {
        $this->writable = $writable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExecutable(): bool
    {
        return $this->executable;
    }

    /**
     * @param bool $executable
     * @return $this
     */
    public function setExecutable(bool $executable): self
    {
        $this->executable = $executable;
        return $this;
    }
}
