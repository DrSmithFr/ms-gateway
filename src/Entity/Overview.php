<?php

declare(strict_types=1);

namespace App\Entity;

use RuntimeException;
use ReflectionException;
use App\Enum\OverviewEventEnum;
use App\Enum\OverviewFeelingEnum;
use App\Entity\Interfaces\SerializableEntity;
use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @JMS\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="App\Repository\OverviewRepository")
 * @ORM\Table(name="overviews")
 */
class Overview implements SerializableEntity
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
     * @var integer|null
     * @ORM\Column(type="integer", name="mood")
     * @Assert\LessThanOrEqual(10)
     * @Assert\GreaterThanOrEqual(0)
     */
    private $mood;

    /**
     * @var array|string[]
     * @ORM\Column(type="json_array", name="feelings")
     */
    private $feelings;

    /**
     * @var array|string[]
     * @ORM\Column(type="json_array", name="events")
     */
    private $events;

    /**
     * @var string|null
     * @ORM\Column(type="text", name="note")
     */
    private $note;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="overviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Overview constructor.
     */
    public function __construct()
    {
        $this->feelings = [];
        $this->events   = [];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMood(): ?int
    {
        return $this->mood;
    }

    /**
     * @param int|null $mood
     * @return self
     */
    public function setMood(?int $mood): self
    {
        $this->mood = $mood;
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getFeelings(): array
    {
        return $this->feelings;
    }

    /**
     * @param array|string[] $feelings
     * @return self
     */
    public function setFeelings(array $feelings): self
    {
        $this->feelings = $feelings;
        return $this;
    }

    /**
     * @throws ReflectionException
     * @param string $feeling
     * @return self
     */
    public function addFeeling(string $feeling): self
    {
        if (!OverviewFeelingEnum::isValidValue($feeling)) {
            throw new RuntimeException('invalid feeling');
        }

        if (!in_array($feeling, $this->feelings, true)) {
            $this->feelings[] = $feeling;
        }

        return $this;
    }

    /**
     * @param string $feeling
     * @return self
     */
    public function removeFeeling(string $feeling): self
    {
        if (($key = array_search($feeling, $this->feelings, true)) !== false) {
            array_splice($this->feelings, $key, 1);
        }

        return $this;
    }


    /**
     * @return array|string[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param array|string[] $events
     * @return self
     */
    public function setEvents(array $events): self
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @throws ReflectionException
     * @param string $event
     * @return self
     */
    public function addEvent(string $event): self
    {
        if (!OverviewEventEnum::isValidValue($event)) {
            throw new RuntimeException('invalid event');
        }

        if (!in_array($event, $this->events, true)) {
            $this->events[] = $event;
        }

        return $this;
    }

    /**
     * @param string $event
     * @return self
     */
    public function removeEvent(string $event): self
    {
        if (($key = array_search($event, $this->events, true)) !== false) {
            array_splice($this->events, $key, 1);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     * @return self
     */
    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        if ($this->getId()) {
            return (string)$this->getId();
        }

        return 'undefined';
    }
}
