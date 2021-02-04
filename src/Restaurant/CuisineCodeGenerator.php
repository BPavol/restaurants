<?php

namespace App\Restaurant;

use Symfony\Component\String\Slugger\SluggerInterface;

class CuisineCodeGenerator
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
     * Generate unique code for cuisine from title.
     *
     * @param string $title
     * @return string
     */
    public function generate(string $title)
    {
        $slug = $this->slugger->slug($title, '')->lower();
        return (string) $slug->slice(0, 16);
    }
}