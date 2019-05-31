<?php

namespace App\Entity\Traits;

use DateTime;
use Exception;
use JMS\Serializer\Annotation as JMS;

/**
 * Trait SoftDeleteableTrait
 *
 * @package App\Entity\Traits
 */
trait SoftDeleteableTrait
{
    /**
     * @JMS\XmlElement(cdata=false)
     * @JMS\Exclude()
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * Sets deletedAt.
     *
     * @param DateTime|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(DateTime $deletedAt = null): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDeleted(): self
    {
        $this->setDeletedAt(new DateTime());
        return $this;
    }

    /**
     * Returns deletedAt.
     *
     * @return DateTime
     */
    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}
