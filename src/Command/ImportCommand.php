<?php

namespace App\Command;

use App\Restaurant\DataTransformer\RestaurantDataTransformer;
use App\Restaurant\Import\RestaurantImporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ImportCommand extends Command
{
    /**
     * Where is imported files stored.
     */
    private const DIR = __DIR__ . '/../../data/';

    private const BATCH_SIZE = 20;

    protected static $defaultName = 'app:import';

    /**
     * @var DecoderInterface
     */
    private DecoderInterface $decoder;

    /**
     * @var RestaurantImporterInterface
     */
    private RestaurantImporterInterface $importer;

    /**
     * @var RestaurantDataTransformer
     */
    private RestaurantDataTransformer $dataTransformer;

    private Stopwatch $stopWatch;

    public function __construct(
        string $name = null,
        DecoderInterface $decoder,
        RestaurantImporterInterface $importer,
        RestaurantDataTransformer $dataTransformer
    )
    {
        parent::__construct($name);

        $this->decoder = $decoder;
        $this->importer = $importer;
        $this->stopWatch = new Stopwatch();
        $this->dataTransformer = $dataTransformer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import CSV')
            ->setHelp(
                <<<EOT
This command allows you to import CSV with restaurant informations and opening hours. 
CSV structure "with-header":
"Restaurant name" | "Restaurant ID" | "Cuisine" | "Opens" | "Closes" | "Days Open" | "Price" | "Rating" | "Location" | "Description"                
CSV structure "headless":
Without header, example: Kushi Tsuru | Mon-Sun 11:30 am - 9 pm
EOT
            )
            ->addArgument('filename', InputArgument::REQUIRED, sprintf('Filename from %s', realpath(self::DIR)))
            ->addArgument('type', InputArgument::REQUIRED, 'Type of imported file(with-header/headless)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->stopWatch->start('main');
        $filename = $input->getArgument('filename');
        $type = $input->getArgument('type');

        $output->writeln([
            sprintf('CSV restaurants importer - %s', $type),
            '============',
            '',
        ]);

        $finder = new Finder();
        $finder->files()
            ->in(realpath(self::DIR))
            ->name($filename);
        if (!$finder->hasResults()) {
            $output->writeln(sprintf('File not found in directory %s', realpath(self::DIR)));
            return Command::FAILURE;
        }

        foreach ($finder as $file) {
            $csvFile = $file;
        }

        switch ($type) {
            case 'with-header':
                $data = $this->decoder->decode($csvFile->getContents(), 'csv', [
                    'as_collection' => true
                ]);
                break;
            case 'headless':
                $data = $this->decoder->decode($csvFile->getContents(), 'csv', [
                    'as_collection' => true,
                    'no_headers' => true
                ]);
                break;
            default:
                $output->writeln('Unrecognized import type');
                return Command::FAILURE;
        }


        return $this->import($data, $output, $type);
    }

    protected function import(iterable $data, OutputInterface $output, string $type)
    {
        $output->writeln('Progress started:');
        $progressSection = $output->section();
        $statsSection = $output->section();
        $this->updateStats($this->importer->getStats(), $statsSection);
        $rowsCount = count($data);
        for ($i = 0; $i < $rowsCount; $i++) {
            if (($i + 1) % self::BATCH_SIZE === 0) {
                $this->importer->flush();
                $this->updateStats($this->importer->getStats(), $statsSection);
            }

            $this->importer->import($this->dataTransformer->transform($data[$i], $type));
            $this->updateProgress($i, $rowsCount, $progressSection);
            usleep(200000);
        }
        $this->importer->flush();
        $this->updateStats($this->importer->getStats(), $statsSection);
        $output->writeln('');

        $stopwatchEvent = $this->stopWatch->stop('main');
        $output->writeln(sprintf('Progress finished in %.3f seconds.', $stopwatchEvent->getDuration() / 1000));

        return Command::SUCCESS;
    }

    private function updateStats(iterable $stats, OutputInterface $output)
    {
        $output->overwrite(sprintf(
            'Restaurants imported/updated: %d/%d | Cuisines imported: %d | Opening hours imported/duplicate: %d/%d | Errors: %d',
            $stats['restaurants_imported'],
            $stats['restaurants_updated'],
            $stats['cuisines_imported'],
            $stats['opening_hours_imported'],
            $stats['opening_hours_duplicate'],
            $stats['errors']
        ));
    }

    private function updateProgress(int $i, int $total, OutputInterface $output)
    {
        $current = round((($i + 1) / $total) * 100);
        $progressInfo = str_pad(str_repeat(':', $current), 100, '.');
        $memoryLimit = ini_get('memory_limit');
        $memoryInfo = sprintf('(%dMb/%s)', round(memory_get_usage() / 1024 / 1024), $memoryLimit);
        $output->overwrite(sprintf('[%s] %s', $progressInfo, $memoryInfo));
    }
}
