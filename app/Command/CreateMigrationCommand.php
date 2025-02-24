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
        $this->addArgument('directory', InputArgument::OPTIONAL, 'The plugin directory (e.g., myplugin)');

    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $name = $input->getArgument('name');
        $dir = $input->getArgument('directory');

        $outputDir = $this->migrationBuilder->create($name, $dir);

        $output->writeln(sprintf('<info>Migration name :</info> %s', $name));
        $output->writeln(sprintf('<info>Migration directory :</info> %s', $dir));

        return Command::SUCCESS;
    }
}