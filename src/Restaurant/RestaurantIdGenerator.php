<?php

namespace App\Restaurant;

use Symfony\Component\String\Slugger\SluggerInterface;

class RestaurantIdGenerator
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * Generate unique identifier for restaurant from title.
     *
     * @param string $title
     * @return string
     */
    public function generate(string $title)
    {
        $slug = $this->slugger->slug($title, '', 'en')->lower();
        $slug = $slug->replace('restaurant', '');
        if ($slug->isEmpty()) {
            return (string) crc32($title);
        }

        if ($slug->length() > 16) {
            $crc = (string) abs(crc32($title));
            $slug = $slug->slice(0, 16 - strlen($crc) - 1);
            $slug = $slug->append('-' . $crc);
        }

        return (string) $slug;
    }
}
