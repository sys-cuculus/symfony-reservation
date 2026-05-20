<?php

namespace App\Entity;

use App\Enum\DayOfWeek;
use App\Repository\OpeningHourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: OpeningHourRepository::class)]
class OpeningHour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'openingHours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\Column(enumType: DayOfWeek::class)]
    private ?DayOfWeek $dayOfWeek = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $closeTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $openTime = null;

    #[ORM\Column(nullable: true)]
    private ?bool $closedFlag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getDayOfWeek(): ?DayOfWeek
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(DayOfWeek $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getCloseTime(): ?\DateTime
    {
        return $this->closeTime;
    }

    public function setCloseTime(?\DateTime $closeTime): static
    {
        $this->closeTime = $closeTime;

        return $this;
    }

    public function getOpenTime(): ?\DateTime
    {
        return $this->openTime;
    }

    public function setOpenTime(?\DateTime $openTime): static
    {
        $this->openTime = $openTime;

        return $this;
    }

    public function isClosedFlag(): ?bool
    {
        return $this->closedFlag;
    }

    public function setClosedFlag(?bool $closedFlag): static
    {
        $this->closedFlag = $closedFlag;

        return $this;
    }

    #[Assert\Callback]
    public function validateOpeningTimes(ExecutionContextInterface $context): void
    {
        if ($this->isClosedFlag()) {
            return;
        }

        if ($this->openTime === null) {
            $context->buildViolation('Open time is required')
                ->atPath('openTime')
                ->addViolation();
        }

        if ($this->closeTime === null) {
            $context->buildViolation('Close time is required')
                ->atPath('closeTime')
                ->addViolation();
        }
    }
}
