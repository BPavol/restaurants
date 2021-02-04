<?php

namespace App\Restaurant;

use App\Entity\OpeningHour;
use DateTimeImmutable;

class OpeningHourManager
{
    /**
     * Search in $openingHours array and return
     * first one that range includes current day and time.
     *
     * @param OpeningHour[]|iterable $openingHours
     * @return OpeningHour|null
     */
    public function findOpenedNowOpeningHour(iterable $openingHours): ?OpeningHour
    {
        $currentDate = DateTimeImmutable::createFromFormat('!D H:i', date('D H:i'));
        foreach ($openingHours as $openingHour) {
            if ($currentDate >= $openingHour->getOpen() && $currentDate <= $openingHour->getClose()) {
                return $openingHour;
            }
        }

        return null;
    }
}
