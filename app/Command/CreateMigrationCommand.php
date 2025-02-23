<?php

namespace NikoGin\Command;

use NikoGin\Builders\MigrationBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:migration',
    description: 'Create a migration for wp database'
)]
class CreateMigrationCommand extends Command
{
    public function __construct(private MigrationBuilder $migrationBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $name = $input->getArgument('name');

        $output->writeln(sprintf('<info>Migration name :</info> %s', $name));


        return Command::SUCCESS;
    }
}