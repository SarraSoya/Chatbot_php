<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class ImportIntentsCommand extends Command
{
    protected static $defaultName = 'app:import-intents';

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::$defaultName)
             ->setDescription('Imports intents from JSON into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Command logic here
        $output->writeln('Importing intents...');
        return Command::SUCCESS;
    }
}
