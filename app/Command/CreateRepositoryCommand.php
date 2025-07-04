<?php

namespace NikoGin\Command;

use NikoGin\Builders\ControllerBuilder;
use NikoGin\Builders\RepositoryBuilder;
use NikoGin\Core\Support\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
  name: 'make:repository',
  description: 'Create a repository',
)]
class CreateRepositoryCommand extends Command
{
    public function __construct(private readonly RepositoryBuilder $builder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the repository.');
        $this->addArgument('table', InputArgument::REQUIRED, 'The table name of the repository.');
        $this->addArgument('dir', InputArgument::REQUIRED, 'The path to the plugin directory.');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $table = strtolower($input->getArgument('table'));
        $dir = strtolower($input->getArgument('dir'));

        // TODO: Handle case for all commands when file already exists
        $controllerDir = $this->builder->create($name, $table, $dir);
        $output->writeln(sprintf('<info>Creating Repository in:</info> %s', $controllerDir));
        $output->writeln(sprintf('<info>Repository Name:</info> %s for table %s', $name, $table));


        return Command::SUCCESS;
    }

}