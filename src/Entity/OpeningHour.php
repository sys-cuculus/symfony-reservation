<?php

namespace App\Entity;

use App\Repository\OpeningHourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $close_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $open_time = null;

    #[ORM\Column(nullable: true)]
    private ?bool $closed_flag = null;

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCloseTime(): ?\DateTime
    {
        return $this->close_time;
    }

    public function setCloseTime(\DateTime $close_time): static
    {
        $this->close_time = $close_time;

        return $this;
    }

    public function getOpenTime(): ?\DateTime
    {
        return $this->open_time;
    }

    public function setOpenTime(\DateTime $open_time): static
    {
        $this->open_time = $open_time;

        return $this;
    }

    public function isClosedFlag(): ?bool
    {
        return $this->closed_flag;
    }

    public function setClosedFlag(?bool $closed_flag): static
    {
        $this->closed_flag = $closed_flag;

        return $this;
    }
}
