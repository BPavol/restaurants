<?php

namespace App\Restaurant\Import;

use DateTimeImmutable;

/**
 * Store entities before flushing and checks for duplicates.
 */
class ImportHashMap
{
    private array $map = [];

    /**
     * @param string $identifier
     * @return string
     */
    public function createRestaurantHash(string $identifier): string
    {
        return sprintf('r_%s', $identifier);
    }

    /**
     * @param string $code
     * @return string
     */
    public function createCuisineHash(string $code): string
    {
        return sprintf('c_%s', $code);
    }

    /**
     * Create unique hash with fields combination, that must be unique.
     *
     * @param string $restaurantIdentifier
     * @param DateTimeImmutable $openingTime
     * @param DateTimeImmutable $closingTime
     * @return string
     */
    public function createOpeningHourHash(string $restaurantIdentifier, DateTimeImmutable $openingTime, DateTimeImmutable $closingTime): string
    {
        return sprintf('oh_%s_%s_%s', $restaurantIdentifier, (int) $openingTime->format('N H:i'), (int) $closingTime->format('N H:i'));
    }

    /**
     * @param $hash
     * @param object $entity
     */
    public function addEntity($hash, object $entity): void
    {
        $this->map[$hash] = $entity;
    }

    /**
     * Retrieve entity from hash map by coresponding hash.
     *
     * @param $hash
     * @return object|null
     */
    public function getEntity($hash): ?object
    {
        if (!isset($this->map[$hash])) {
            return null;
        }

        return $this->map[$hash];
    }

    public function clear(): void
    {
        $this->map = [];
    }
}