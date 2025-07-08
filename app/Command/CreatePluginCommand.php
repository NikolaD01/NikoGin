<?php

namespace NikoGin\Command;

use NikoGin\Builders\PluginBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        $this->addArgument('pluginName', InputArgument::REQUIRED, 'The name of the plugin (e.g., My Awesome Plugin)');
        $this->addArgument('pluginPrefix', InputArgument::REQUIRED, 'The prefix for the plugin namespace (e.g., MyPluginPrefix)');
        $this->addArgument('path', InputArgument::OPTIONAL, 'The path to the WordPress root directory. Defaults to current directory.', '.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pluginName = $input->getArgument('pluginName');
        $pluginPrefix = $input->getArgument('pluginPrefix');
        $wordpressPath = $input->getArgument('path');


        $pluginsRoot = $wordpressPath . '/wp-content/plugins';

        if (!is_dir($pluginsRoot)) {
            $output->writeln("<error>Could not find 'wp-content/plugins' directory in '{$wordpressPath}'. Is this a valid WordPress installation?</error>");
            return Command::FAILURE;
        }

        //NOTE: Instead of this strtolower + preg_replace + str_replace symphony/string composer library can be required
        // and this could be replaced with: $directoryName = $this->slugger->slug($pluginName)->lower().
        $directorySlug = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(' ', '-', $pluginName)));
        $pluginDir = $pluginsRoot . '/' . $directorySlug;

        if (is_dir($pluginDir)) {
            $output->writeln("<error>Plugin directory '{$pluginName}' already exists at '{$pluginsRoot}'.</error>");
            return Command::FAILURE;
        }

        try {
            $this->pluginBuilder->create($pluginName, $pluginPrefix, $pluginDir, $directorySlug);
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