<?php

namespace NikoGin\Command;

use NikoGin\Builders\ProviderBuilder;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:provider',
    description: 'Create a provider for dependencies'
)]
class CreateProviderCommand extends Command
{
    public function __construct(private readonly ProviderBuilder $providerBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the provider');
        $this->addArgument('directory', InputArgument::OPTIONAL, 'The plugin directory (e.g., myplugin)');

    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $name = $input->getArgument('name');
        $dir = $input->getArgument('directory');

        $outputDir = $this->providerBuilder->create($name, $dir);

        $output->writeln(sprintf('<info>Provider name :</info> %s', $name));
        $output->writeln(sprintf('<info>Provider directory :</info> %s', $dir));

        return Command::SUCCESS;
    }
}