<?php

namespace App\Restaurant\Import;

interface RestaurantImporterInterface
{
    /**
     * Current progress stats.
     *
     * @return array
     */
    public function getStats(): array;

    /**
     * Create coresponding entities, relations
     * and persist them for future flush in batches.
     *
     * @param iterable $row
     * @return void
     */
    public function import(iterable $row): void;

    /**
     * Flush and free memory.
     *
     * @return mixed
     */
    public function flush(): void;
}
