<?php

namespace NikoGin\Command;

use NikoGin\Builders\PluginBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'create',
    description: 'Create a new WordPress plugin structure'
)]
class CreatePluginCommand extends Command
{
    public function __construct(private readonly PluginBuilder $pluginBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('pluginName', InputArgument::OPTIONAL, 'The name of the plugin');
        $this->addArgument('pluginPrefix', InputArgument::OPTIONAL, 'The prefix for the plugin namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pluginName = $input->getArgument('pluginName');
        $pluginPrefix = $input->getArgument('pluginPrefix');

        if (!$pluginName || !$pluginPrefix) {
            $helper = $this->getHelper('question');

            if (!$pluginName) {
                $question = new Question('Please enter the plugin name: ');
                $pluginName = $helper->ask($input, $output, $question);
            }

            if (!$pluginPrefix) {
                $question = new Question('Please enter the plugin prefix (e.g., MyPlugin): ');
                $pluginPrefix = $helper->ask($input, $output, $question);
            }
        }

        try {
            $pluginDir = $this->pluginBuilder->create($pluginName, $pluginPrefix);
            $output->writeln("<info>Plugin directory created at: {$pluginDir}</info>");
            $output->writeln("<info>composer.json created with prefix: {$pluginPrefix}</info>");

            $process = new Process(['composer', 'install'], $pluginDir);
            $process->run();

            if (!$process->isSuccessful()) {
                $output->writeln("<error>Composer install failed: {$process->getErrorOutput()}</error>");
                return Command::FAILURE;
            }

            $output->writeln("<info>Composer install completed successfully.</info>");

        } catch (\RuntimeException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}