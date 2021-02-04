<?php

namespace App\Restaurant\Factory;

use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class RestaurantFactory
{
    private const SCALARS = [
        'title',
        'identifier',
        'price',
        'rating',
        'location',
        'description'
    ];

    /**
     * @var PropertyAccessorInterface
     */
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Create entity with prefilled fields.
     *
     * @param iterable $row
     * @return Restaurant
     */
    public function create(iterable $row): Restaurant
    {
        $restaurant = new Restaurant();
        $this->prefillScalars($restaurant, $row);

        return $restaurant;
    }

    /**
     * Prefill Restaurant object with scalars if provided in $row.
     *
     * @param Restaurant $restaurant
     * @param iterable $row
     */
    public function prefillScalars(Restaurant $restaurant, iterable $row): void
    {
        foreach (self::SCALARS as $key) {
            if (!isset($row[$key])) {
                continue;
            }

            $this->propertyAccessor->setValue($restaurant, $key, $row[$key]);
        }
    }
}
