<?php

namespace NikoGin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'make:controller',
    description: 'Create a Controller for dashboard or Rest API'
)]
class CreateControllerCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the controller');
        $this->addArgument('type', InputArgument::REQUIRED, 'The type of the controller');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $type = $input->getArgument('type');

        $output->writeln(sprintf('<info>Creating Controller:</info> %s (%s)', $name, $type));

        return Command::SUCCESS;
    }

}