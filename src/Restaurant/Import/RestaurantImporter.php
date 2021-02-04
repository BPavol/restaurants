<?php

namespace App\Restaurant\Import;

use App\Entity\Cuisine;
use App\Entity\OpeningHour;
use App\Entity\Restaurant;
use App\Restaurant\CuisineCodeGenerator;
use App\Restaurant\Factory\CuisineFactory;
use App\Restaurant\Factory\OpeningHourFactory;
use App\Restaurant\Factory\RestaurantFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class RestaurantImporter implements RestaurantImporterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ImportHashMap
     */
    private ImportHashMap $importHashMap;

    /**
     * @var CuisineCodeGenerator
     */
    private CuisineCodeGenerator $cuisineCodeGenerator;

    /**
     * @var RestaurantFactory
     */
    private RestaurantFactory $restaurantFactory;

    /**
     * @var CuisineFactory
     */
    private CuisineFactory $cuisineFactory;

    /**
     * @var OpeningHourFactory
     */
    private OpeningHourFactory $openingHourFactory;

    /**
     * @var array
     */
    private array $batch = [];

    private array $stats = [
        'restaurants_imported' => 0,
        'restaurants_updated' => 0,
        'cuisines_imported' => 0,
        'opening_hours_imported' => 0,
        'opening_hours_duplicate' => 0,
        'errors' => 0
    ];

    public function __construct(
        EntityManagerInterface $entityManager,
        ImportHashMap $importHashMap,
        CuisineCodeGenerator $cuisineCodeGenerator,
        RestaurantFactory $restaurantFactory,
        CuisineFactory $cuisineFactory,
        OpeningHourFactory $openingHourFactory)
    {
        $this->entityManager = $entityManager;
        $this->importHashMap = $importHashMap;
        $this->cuisineCodeGenerator = $cuisineCodeGenerator;
        $this->restaurantFactory = $restaurantFactory;
        $this->cuisineFactory = $cuisineFactory;
        $this->openingHourFactory = $openingHourFactory;
    }

    /**
     * @return array|int[]
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * @inheritDoc
     *
     * @param iterable $row
     * @return void
     */
    public function import(iterable $row): void
    {
        $restaurant = $this->createRestaurant($row);
        if (isset($row['cuisine'])) {
            $cuisine = $this->createCuisine($row['cuisine']);
            $restaurant->setCuisine($cuisine);
        }
        $openingHours = $this->createOpeningHours($restaurant, $row['openingHours']);

        // Unlisted opening hours will be deleted(orphanRemoval=true)
        $restaurant->setOpeningHours($openingHours);

        $this->batch[] = $restaurant;
    }

    /**
     * Check if restaurants exists.
     * If not create one, otherwise return it.
     *
     * @param iterable $row
     * @return Restaurant
     */
    private function createRestaurant(iterable $row): Restaurant
    {
        $restaurantRepository = $this->entityManager->getRepository(Restaurant::class);
        $hash = $this->importHashMap->createRestaurantHash($row['identifier']);
        /** @var Restaurant $restaurant */
        $restaurant = $this->importHashMap->getEntity($hash) ?? $restaurantRepository->findOneBy([
            'identifier' => $row['identifier']
        ]);
        if ($restaurant !== null) {
            $this->restaurantFactory->prefillScalars($restaurant, $row);
            $this->stats['restaurants_updated']++;
            return $restaurant;
        }

        $restaurant = $this->restaurantFactory->create($row);
        $this->entityManager->persist($restaurant);
        $this->importHashMap->addEntity($hash, $restaurant);
        $this->stats['restaurants_imported']++;
        return $restaurant;
    }

    /**
     * @param $title
     * @return Cuisine
     */
    private function createCuisine($title)
    {
        $code = $this->cuisineCodeGenerator->generate($title);
        $cuisineRepository = $this->entityManager->getRepository(Cuisine::class);
        $hash = $this->importHashMap->createCuisineHash($code);
        /** @var Cuisine $cuisine */
        $cuisine = $this->importHashMap->getEntity($hash) ?? $cuisineRepository->find($code);
        if ($cuisine !== null) {
            return $cuisine;
        }

        $cuisine = $this->cuisineFactory->create($title, $code);
        $this->entityManager->persist($cuisine);
        $this->importHashMap->addEntity($hash, $cuisine);
        $this->stats['cuisines_imported']++;
        return $cuisine;
    }

    /**
     * @param Restaurant $restaurant
     * @param iterable $openingHoursDates
     * @return Collection
     */
    private function createOpeningHours(Restaurant $restaurant, iterable $openingHoursDates): Collection
    {
        $openingHours = new ArrayCollection();
        foreach ($openingHoursDates as $dates) {
            $openingTime = $dates['openingTime'];
            $closingTime = $dates['closingTime'];
            $hash = $this->importHashMap->createOpeningHourHash($restaurant->getIdentifier(), $openingTime, $closingTime);

            /** @var OpeningHour $openingHour */
            $openingHour = $this->importHashMap->getEntity($hash);
            // Opening hour for that day already exists, update it
            if ($openingHour !== null) {
                $this->stats['opening_hours_duplicate']++;
                continue;
            }

            $this->stats['opening_hours_imported']++;
            $openingHour = $this->openingHourFactory->create($openingTime, $closingTime);
            $this->entityManager->persist($openingHour);
            $this->importHashMap->addEntity($hash, $openingHour);
            $openingHours->add($openingHour);
        }

        return $openingHours;
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->batch = [];
        $this->importHashMap->clear();
    }
}