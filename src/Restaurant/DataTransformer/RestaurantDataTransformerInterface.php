<?php

namespace App\Restaurant\DataTransformer;

/**
 * By implementing this interface, class is automatically
 * taged as possible transformer for restaurants data.
 */
interface RestaurantDataTransformerInterface
{
    /**
     * Transform array to sameless structure.
     *
     * @param iterable $row
     * @return iterable
     */
    public function transform(iterable $row): iterable;

    /**
     * Which structure of file transformer handles.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool;
}
