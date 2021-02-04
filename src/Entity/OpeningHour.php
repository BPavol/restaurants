<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  name="opening_hour",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="open_close_restaurant_id_uc", columns={"open", "close", "restaurant_id"})
 *  },
 *  indexes={
 *      @ORM\Index(name="open_idx", columns={"open"})
 *  }
 * )
 */
class OpeningHour
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Restaurant
     *
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="openingHours")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Restaurant $restaurant;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $open;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $close;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function setRestaurant(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getOpen(): DateTimeImmutable
    {
        return $this->open;
    }

    /**
     * @param DateTimeImmutable $open
     */
    public function setOpen(DateTimeImmutable $open): void
    {
        $this->open = $open;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getClose(): DateTimeImmutable
    {
        return $this->close;
    }

    /**
     * @param DateTimeImmutable $close
     */
    public function setClose(DateTimeImmutable $close): void
    {
        $this->close = $close;
    }
}
