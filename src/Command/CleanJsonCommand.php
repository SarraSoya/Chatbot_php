<?php

namespace App\Command;

use App\Service\TextCleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clean-json',
    description: 'Cleans text fields in intents.json and saves the cleaned data to intents_cleaned.json',
)]
class CleanJsonCommand extends Command
{
    private $textCleaner;

    public function __construct(TextCleaner $textCleaner)
    {
        $this->textCleaner = $textCleaner;
        parent::__construct();
    }

    protected function configure(): void
    {
        // No additional arguments or options needed
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Updated path for intents.json in the public directory
        $jsonFilePath = __DIR__ . '/../../public/intents.json';
        if (!file_exists($jsonFilePath)) {
            $io->error("File intents.json not found in the public directory.");
            return Command::FAILURE;
        }
        
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error('Invalid JSON format: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        // Clean each text field in the JSON data
        foreach ($data['intents'] as &$intent) {
            if (isset($intent['patterns'])) {
                foreach ($intent['patterns'] as &$pattern) {
                    $pattern = $this->textCleaner->clean($pattern);
                }
            }
            if (isset($intent['responses'])) {
                foreach ($intent['responses'] as &$response) {
                    $response = $this->textCleaner->clean($response);
                }
            }
        }

        // Save the cleaned data to a new JSON file in the public directory
        $cleanedFilePath = __DIR__ . '/../../public/intents_cleaned.json';
        file_put_contents($cleanedFilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $io->success("The data has been cleaned and saved to intents_cleaned.json in the public directory.");

        return Command::SUCCESS;
    }
}
