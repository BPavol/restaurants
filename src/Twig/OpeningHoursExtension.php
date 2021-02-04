<?php

namespace App\Twig;

use App\Entity\Restaurant;
use App\Restaurant\OpeningHourManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OpeningHoursExtension extends AbstractExtension
{
    /**
     * @var OpeningHourManager
     */
    private OpeningHourManager $openingHourManager;

    public function __construct(OpeningHourManager $openingHourManager)
    {
        $this->openingHourManager = $openingHourManager;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('opened_now', [$this, 'openedNow']),
        ];
    }

    /**
     * Return first OpenHour that is currently opened.
     *
     * @param Restaurant $restaurant
     * @return \App\Entity\OpeningHour|null
     */
    public function openedNow(Restaurant $restaurant)
    {
        return $this->openingHourManager->findOpenedNowOpeningHour($restaurant->getOpeningHours());
    }
}
