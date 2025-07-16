<?php

namespace NikoGin\Command;

use NikoGin\Builders\MiddlewareBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:middleware',
    description: 'Create a Wordpress request Middleware'
)]
class CreateMiddlewareCommand extends Command
{
    public function __construct(private readonly MiddlewareBuilder $middlewareBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the Middleware');
        $this->addArgument('directory', InputArgument::OPTIONAL, 'The plugin directory (e.g., myplugin)');

    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $name = $input->getArgument('name');
        $dir = $input->getArgument('directory');

        $outputDir = $this->middlewareBuilder->create($name, $dir);

        $output->writeln(sprintf('<info>Migration name :</info> %s', $name));
        $output->writeln(sprintf('<info>Migration directory :</info> %s', $dir));

        return Command::SUCCESS;
    }
}