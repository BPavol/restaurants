<?php

namespace App\Restaurant\DataTransformer;

class RestaurantDataTransformer
{
    /**
     * @var iterable
     */
    private iterable $transformers;

    public function __construct(iterable $transformers)
    {
        $this->transformers = $transformers;
    }

    /**
     * Search for supported transformer
     * and apply transformation to row.
     *
     * @param iterable $row
     * @param string $format
     * @return iterable
     * @throws RuntimeException
     */
    public function transform(iterable $row, string $format): iterable
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($format)) {
                return $transformer->transform($row, $format);
            }
        }

        throw new RuntimeException(sprintf('No transformer found for format "%s".', $format));
    }
}
