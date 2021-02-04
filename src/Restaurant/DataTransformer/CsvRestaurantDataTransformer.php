<?php

namespace App\Restaurant\DataTransformer;

use DateTimeImmutable;

class CsvRestaurantDataTransformer implements RestaurantDataTransformerInterface
{
    private const TRANSFORM_MAP = [
        'Restaurant name' => 'title',
        'Restaurant ID' => 'identifier',
        'Cuisine' => 'cuisine',
        'Opens' => 'opens',
        'Closes' => 'closes',
        'Days Open' => 'daysOpen',
        'Price' => 'price',
        'Rating' => 'rating',
        'Location' => 'location',
        'Description' => 'description'
    ];

    private const DAYS_MAP = [
        'Mo' => 1,
        'Tu' => 2,
        'We' => 3,
        'Th' => 4,
        'Fr' => 5,
        'Sa' => 6,
        'Su' => 7,
    ];

    /**
     * @param iterable $row
     * @return iterable
     */
    public function transform(iterable $row): iterable
    {
        $transformedRow = [];
        foreach ($row as $key => $value) {
            if (!isset(self::TRANSFORM_MAP[$key])) {
                continue;
            }

            $transformedRow[self::TRANSFORM_MAP[$key]] = $value;
        }

        $openingTime = DateTimeImmutable::createFromFormat('!H:i:s', trim($transformedRow['opens']));
        $closingTime = DateTimeImmutable::createFromFormat('!H:i:s', trim($transformedRow['closes']));
        $days = $this->parseDays($transformedRow['daysOpen']);

        $transformedRow['openingHours'] = $this->createOpeningTimes($openingTime, $closingTime, $days);
        return $transformedRow;
    }

    /**
     * @param string $daysStr
     * @return array
     */
    private function parseDays(string $daysStr): array
    {
        $daysParts = explode(',', $daysStr);
        $days = [];
        foreach ($daysParts as $dayStr) {
            if (!isset(self::DAYS_MAP[trim($dayStr)])) {
                continue;
            }

            $days[] = self::DAYS_MAP[trim($dayStr)];
        }

        return $days;
    }

    /**
     * Create array with opening/closing dates.
     * Dates are reseted to UNIX epoch and
     * only day and time are set.
     *
     * @param DateTimeImmutable $openingTime
     * @param DateTimeImmutable $closingTime
     * @param iterable $days
     * @return array
     */
    private function createOpeningTimes(DateTimeImmutable $openingTime, DateTimeImmutable $closingTime, iterable $days)
    {
        // Opened until next day
        $openedUntilNextDay = false;
        if ($openingTime > $closingTime) {
            $openedUntilNextDay = true;
        }

        $dates = [];
        foreach ($days as $day) {
            $open = (clone $openingTime)->setDate(1970, 1, $day - 1);
            $close = (clone $closingTime)->setDate(1970, 1, $day - 1);
            if ($openedUntilNextDay) {
                $close = $close->setDate(1970, 1, $day + 1 > 7 ? 0 : $day);
            }

            $dates[] = [
                'openingTime' => $open,
                'closingTime' => $close
            ];
        }

        return $dates;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'with-header';
    }
}
