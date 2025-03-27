<?php

namespace NikoGin\Command;

use NikoGin\Builders\CronBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:cron',
    description: 'Create wordpress cron'
)]
class CreateCronCommand extends Command
{
    public function __construct(private readonly CronBuilder $cronBuilder)
    {
        parent::__construct();
    }

    protected function configure() : void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
        $this->addArgument('directory', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $directory = $input->getArgument('directory');

        $dir = $this->cronBuilder->create($name, $directory);

        $output->writeln("<info>Created {$name} Cron in : {$dir}</info>");

        return Command::SUCCESS;
    }
}