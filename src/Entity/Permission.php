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
class Permission
{
    /**
     * @var Authorization
     * @ORM\Embedded(class="App\Entity\Authorization", columnPrefix="user_")
     */
    private $user;

    /**
     * @var Authorization
     * @ORM\Embedded(class="App\Entity\Authorization", columnPrefix="group_")
     */
    private $group;

    /**
     * @var Authorization
     * @ORM\Embedded(class="App\Entity\Authorization", columnPrefix="other_")
     */
    private $other;

    /**
     * Authorization constructor.
     *
     * @param Authorization|null $user
     * @param Authorization|null $group
     * @param Authorization|null $other
     */
    public function __construct(
        Authorization $user = null,
        Authorization $group = null,
        Authorization $other = null
    ) {
        if ($user === null) {
            $user = new Authorization(true, true, false);
        }

        if ($group === null) {
            $group = new Authorization(true, false, false);
        }

        if ($other === null) {
            $other = new Authorization(false, false, false);
        }

        $this->user  = $user;
        $this->group = $group;
        $this->other = $other;
    }

    /**
     * @return Authorization
     */
    public function getUser(): Authorization
    {
        return $this->user;
    }

    /**
     * @param Authorization $user
     * @return $this
     */
    public function setUser(Authorization $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Authorization
     */
    public function getGroup(): Authorization
    {
        return $this->group;
    }

    /**
     * @param Authorization $group
     * @return $this
     */
    public function setGroup(Authorization $group): self
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return Authorization
     */
    public function getOther(): Authorization
    {
        return $this->other;
    }

    /**
     * @param Authorization $other
     * @return $this
     */
    public function setOther(Authorization $other): self
    {
        $this->other = $other;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s%s%s',
            $this->user->__toString(),
            $this->group->__toString(),
            $this->other->__toString()
        );
    }
}
