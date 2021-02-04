<?php

namespace App\Restaurant\Factory;

use App\Entity\Cuisine;
use App\Restaurant\CuisineCodeGenerator;

class CuisineFactory
{
    /**
     * @var CuisineCodeGenerator
     */
    private CuisineCodeGenerator $codeGenerator;

    /**
     * Create entity with prefiled fields.
     *
     * @param string $title
     * @param string $code
     * @return Cuisine
     */
    public function create(string $title, string $code): Cuisine
    {
        $cuisine = new Cuisine();
        if ($code === null) {
            $code = $this->codeGenerator->generate($title);
        }
        $cuisine->setCode($code);
        $cuisine->setTitle($title);

        return $cuisine;
    }
}
