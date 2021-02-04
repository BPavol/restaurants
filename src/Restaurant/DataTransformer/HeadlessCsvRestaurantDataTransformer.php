<?php

namespace App\Restaurant\DataTransformer;

use App\Restaurant\RestaurantIdGenerator;
use DateTimeImmutable;

class HeadlessCsvRestaurantDataTransformer implements RestaurantDataTransformerInterface
{
    private const DAYS = [
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thu' => 4,
        'fri' => 5,
        'sat' => 6,
        'sun' => 7,
    ];

    /**
     * @var RestaurantIdGenerator
     */
    private RestaurantIdGenerator $restaurantIdGenerator;

    public function __construct(RestaurantIdGenerator $restaurantIdGenerator)
    {
        $this->restaurantIdGenerator = $restaurantIdGenerator;
    }

    /**
     * @param iterable $row
     * @return iterable
     */
    public function transform(iterable $row): iterable
    {
        $parts = explode('/', $row[1]);
        $openingHours = [];
        foreach ($parts as $str) {
            $hours = $this->parseHours($str);
            foreach ($hours as $day => $hour) {
                $openingHours[$day] = $hour;
            }
        }

        return [
            'title' => $row[0],
            'identifier' => $this->restaurantIdGenerator->generate($row[0]),
            'openingHours' => $openingHours,
        ];
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'headless';
    }

    /**
     * @param string $str
     * @return array
     */
    private function parseHours(string $str)
    {
        preg_match('/^([^0-9]+)(?:([^-]*)[-\s]+(.*))$/i', $str, $matches);
        $daysParts = explode(',', $matches[1]);
        $openingTime = $this->parseTime($matches[2]);
        $closingTime = $this->parseTime($matches[3]);
        $days = [];
        foreach ($daysParts as $str) {
            $days = [...$days, ...$this->parseDays($str)];
        }

        return $this->createOpeningTimes($openingTime, $closingTime, $days);
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
     * @param string $str
     * @return array|int[]
     */
    private function parseDays(string $str) {
        $str = strtolower(trim($str));
        if (strpos($str, '-') !== false) {
            return $this->explodeDateRange($str);
        }

        return [self::DAYS[$str]] ?? [];
    }

    /**
     * @param string $str
     * @return DateTimeImmutable|false
     */
    private function parseTime(string $str)
    {
        $str = trim($str);
        if (strpos($str, ':') !== false) {
            return DateTimeImmutable::createFromFormat('!H:i a', $str);
        }

        return DateTimeImmutable::createFromFormat('!H a', $str);
    }

    /**
     * Create array with days number from string date range.
     * E.g. "Mon-Fri" => [1, 2, 3, 4, 5]
     *
     * @param string $str
     * @return array
     */
    private function explodeDateRange(string $str)
    {
        $parts = explode('-', $str);
        $begin = strtolower(trim($parts[0]));
        $end = strtolower(trim($parts[1]));

        if (!isset(self::DAYS[$begin])) {
            return [];
        }
        $beginDay = self::DAYS[$begin];

        if (!isset(self::DAYS[$end])) {
            return [];
        }
        $endDay = self::DAYS[$end];

        if ($beginDay > $endDay) {
            return array_merge(range(0, $endDay), range($beginDay, 6));
        }

        return range($beginDay, $endDay);
    }
}
