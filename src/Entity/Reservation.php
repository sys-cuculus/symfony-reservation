<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[
    ORM\UniqueConstraint(
        name: 'uniq_reservation_restaurant_date_time',
        columns: ['restaurant_id', 'date_and_time']
    ),
]
#[UniqueEntity(
    fields: ['restaurant', 'dateAndTime'],
    message: 'Seats unavailable at this date and time. Please choose another slot.',
    errorPath: 'dateAndTime',
    identifierFieldNames:['id']
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\Column]
    private ?int $numberOfPeople = null;

    #[ORM\Column]
    private ?\DateTime $dateAndTime = null;

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

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getNumberOfPeople(): ?int
    {
        return $this->numberOfPeople;
    }

    public function setNumberOfPeople(int $numberOfPeople): static
    {
        $this->numberOfPeople = $numberOfPeople;

        return $this;
    }

    public function getDateAndTime(): ?\DateTime
    {
        return $this->dateAndTime;
    }

    public function setDateAndTime(\DateTime $dateAndTime): static
    {
        $this->dateAndTime = $dateAndTime;

        return $this;
    }
}
