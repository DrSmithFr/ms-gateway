<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
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
    private $uuid;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="password")
     */
    private $password;

    /**
     * Used internally for login and register form
     * @var string|null
     */
    private $plainPassword;

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
     * @var Collection|Overview[]
     * @ORM\OneToMany(targetEntity="App\Entity\Overview", mappedBy="users")
     */
    private $overviews;

    /**
     * @var UuidInterface|null
     * @ORM\Column(type="uuid", name="transfer_uuid", nullable=true)
     */
    private $transferUuid;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="transfer_password", nullable=true)
     */
    private $transferPassword;

    /**
     * Used to send encrypted data to user
     * @var string|null
     * @ORM\Column(type="text", name="public_gpg", nullable=true)
     */
    private $publicGpg;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->groups    = new ArrayCollection();
        $this->roles     = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->overviews = new ArrayCollection();
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
    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @param UuidInterface|null $uuid
     * @return $this
     */
    public function setUuid(?UuidInterface $uuid): self
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
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return self
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
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
        return $this->getUuid() ? $this->getUuid()->toString() : null;
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

    /**
     * @return Collection|Resource[]
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    /**
     * @param Collection|Resource[] $resources
     * @return self
     */
    public function setResources(Collection $resources): self
    {
        $this->resources = $resources;
        return $this;
    }

    /**
     * @return Overview[]|Collection
     */
    public function getOverviews(): Collection
    {
        return $this->overviews;
    }

    /**
     * @param Overview[]|Collection $overviews
     * @return self
     */
    public function setOverviews(Collection $overviews): self
    {
        $this->overviews = $overviews;
        return $this;
    }

    /**
     * @return UuidInterface|null
     */
    public function getTransferUuid(): ?UuidInterface
    {
        return $this->transferUuid;
    }

    /**
     * @param UuidInterface|null $transferUuid
     *
     * @return self
     */
    public function setTransferUuid(?UuidInterface $transferUuid): self
    {
        $this->transferUuid = $transferUuid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTransferPassword(): ?string
    {
        return $this->transferPassword;
    }

    /**
     * @param string|null $transferPassword
     *
     * @return self
     */
    public function setTransferPassword(?string $transferPassword): self
    {
        $this->transferPassword = $transferPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublicGpg(): ?string
    {
        return $this->publicGpg;
    }

    /**
     * @param string|null $publicGpg
     *
     * @return self
     */
    public function setPublicGpg(?string $publicGpg): self
    {
        $this->publicGpg = $publicGpg;
        return $this;
    }
}
