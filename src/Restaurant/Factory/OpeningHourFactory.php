<?php

namespace App\Restaurant\Factory;

use App\Entity\OpeningHour;
use DateTimeImmutable;

class OpeningHourFactory
{
    /**
     * @param DateTimeImmutable $open
     * @param DateTimeImmutable $close
     * @return OpeningHour
     */
    public function create(DateTimeImmutable $open, DateTimeImmutable $close): OpeningHour
    {
        $openingHour = new OpeningHour();
        $openingHour->setOpen($open);
        $openingHour->setClose($close);

        return $openingHour;
    }
}
