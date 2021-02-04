<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  name="cuisine",
 *  indexes={
 *      @ORM\Index(name="title_idx", columns={"title"}),
 *  }
 * )
 */
class Cuisine
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     * @ORM\Id
     */
    private string $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     */
    private string $title;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
}
