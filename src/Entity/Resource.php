<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 * @ORM\Entity()
 * @ORM\Table(name="resources")
 */
class Resource
{
    use TimestampableTrait;
    use BlameableTrait;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     * @JMS\Expose()
     * @JMS\SerializedName("id")
     * @ORM\Column(type="string")
     */
    private $externalId;

    /**
     * @var Permission
     * @ORM\Embedded(class="App\Entity\Permission", columnPrefix="permission_")
     */
    private $permission;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="resources")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $users;

    /**
     * Resource constructor.
     *
     * @param Permission|null $permission
     */
    public function __construct(Permission $permission = null)
    {
        if ($permission === null) {
            $permission = new Permission();
        }

        $this->permission = $permission;
    }
}
