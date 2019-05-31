<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Enable Blameable fields on entities
 *
 * @package App\Entity\Traits
 * @codeCoverageIgnore
 */
trait BlameableTrait
{

    /**
     * @JMS\Groups({"blameable"})
     * @var string|null
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(nullable=true)
     */
    private $createdBy;

    /**
     * @JMS\Groups({"blameable"})
     * @var string|null
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(nullable=true)
     */
    private $updatedBy;

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
}
