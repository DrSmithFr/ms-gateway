<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\SerializableEntity;
use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @JMS\AccessorOrder("custom", custom = {"externalId", "username" ,"normalizedGroups", "normalizedRoles"})
 * @JMS\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface, SerializableEntity
{
    use TimestampableTrait;
    use BlameableTrait;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UuidInterface|null
     * @JMS\Expose()
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     * @ORM\Column(type="uuid", unique=true)
     */
    private $externalId;

    /**
     * @var string|null
     * @JMS\Expose()
     * @JMS\Type("string")
     * @ORM\Column(type="string", name="email")
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="password")
     */
    private $password;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="salt")
     */
    private $salt;

    /**
     * @var Collection|Group[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="users")
     * @ORM\JoinTable(
     *  name="user_groups",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *  }
     * )
     */
    private $groups;

    /**
     * @var Collection|Role[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @ORM\JoinTable(
     *  name="user_roles",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *     @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *  }
     * )
     */
    private $roles;

    /**
     * @var Collection|Resource[]
     * @ORM\OneToMany(targetEntity="App\Entity\Resource", mappedBy="users")
     */
    private $resources;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->groups    = new ArrayCollection();
        $this->roles     = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }

    /**
     * @JMS\Expose()
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("roles")
     * @JMS\Type("array<string>")
     * @return array
     */
    public function getNormalizedRoles(): array
    {
        $roles = [];

        foreach ($this->groups as $group) {
            foreach ($group->getRoles() as $role) {
                $roles[] = $role->getName();
            }
        }

        foreach ($this->roles as $role) {
            $role[] = $role->getName();
        }

        return array_values(array_unique($roles));
    }

    /**
     * @JMS\Expose()
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("groups")
     * @JMS\Type("array<string>")
     * @return array
     */
    public function getNormalizedGroups(): array
    {
        return array_map(
            static function (Group $group): string {
                return $group->getName();
            },
            $this->groups->toArray()
        );
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return $this
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return UuidInterface|null
     */
    public function getExternalId(): ?UuidInterface
    {
        return $this->externalId;
    }

    /**
     * @param UuidInterface|null $externalId
     * @return $this
     */
    public function setExternalId(?UuidInterface $externalId): self
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
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
     * @return $this
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string|null $salt
     * @return $this
     */
    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return Group[]|Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @param Group[]|Collection $groups
     * @return $this
     */
    public function setGroups(Collection $groups): self
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * @param Group $group
     * @return $this
     */
    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addUser($this);
        }

        return $this;
    }

    /**
     * @param Group $group
     * @return $this
     */
    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeUser($this);
        }

        return $this;
    }

    /**
     * @return array|Role[]
     */
    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    /**
     * @param Role[]|Collection $roles
     * @return $this
     */
    public function setRoles(Collection $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->getId() ?: 'undefined';
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string|null The username
     */
    public function getUsername(): ?string
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        // nothing for now
    }
}
