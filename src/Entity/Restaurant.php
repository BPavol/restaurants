<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  name="restaurant",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="identifier_uc", columns={"identifier"})
 *  },
 *  indexes={
 *      @ORM\Index(name="title_location_fidx", columns={"title", "location"}, flags={"fulltext"}),
 *      @ORM\Index(name="rating_idx", columns={"rating"}),
 *      @ORM\Index(name="price_idx", columns={"price"})
 *  }
 * )
 */
class Restaurant
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
     * @var Cuisine|null
     *
     * @ORM\ManyToOne(targetEntity="Cuisine")
     * @ORM\JoinColumn(name="cuisine_code", referencedColumnName="code", nullable=true, onDelete="SET NULL")
     */
    private ?Cuisine $cuisine;

    /**
     * @var OpeningHour[]|Collection
     *
     * @ORM\OneToMany(targetEntity="OpeningHour", mappedBy="restaurant", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $openingHours;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     */
    private string $identifier;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private string $title;

    /**
     * @var int|null
     *
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned":true})
     */
    private ?int $price;

    /**
     * @var int|null
     *
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned":true})
     */
    private ?int $rating;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $location;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    public function __construct()
    {
        $this->openingHours = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Cuisine|null
     */
    public function getCuisine(): ?Cuisine
    {
        return $this->cuisine;
    }

    /**
     * @param Cuisine|null $cuisine
     */
    public function setCuisine(?Cuisine $cuisine): void
    {
        $this->cuisine = $cuisine;
    }

    /**
     * @return OpeningHour[]|Collection
     */
    public function getOpeningHours(): Collection
    {
        return $this->openingHours;
    }

    /**
     * @param OpeningHour[]|Collection $openingHours
     */
    public function setOpeningHours(Collection $openingHours): void
    {
        foreach ($openingHours as $openingHour) {
            $openingHour->setRestaurant($this);
        }

        $this->openingHours = $openingHours;
    }

    /**
     * @param OpeningHour $openingHour
     */
    public function addOpeningHour(OpeningHour $openingHour): void
    {
        $openingHour->setRestaurant($this);
        $this->openingHours->add($openingHour);
    }

    /**
     * @param OpeningHour $openingHour
     */
    public function removeOpeningHour(OpeningHour $openingHour): void
    {
        $this->openingHours->removeElement($openingHour);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     */
    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     */
    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
