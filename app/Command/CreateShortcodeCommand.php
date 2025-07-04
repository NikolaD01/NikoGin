<?php

namespace NikoGin\Command;

use NikoGin\Builders\ShortcodeBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
#[AsCommand(
    name: 'make:shortcode',
    description: 'Create a Wordpress shortcode.'
)]
class CreateShortcodeCommand extends Command
{
    public function __construct(private readonly ShortcodeBuilder $shortcodeBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the class.');
        $this->addArgument('action', InputArgument::REQUIRED, 'The name of shortcode action.');
        $this->addArgument('directory', InputArgument::REQUIRED, 'The plugin directory.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $action = $input->getArgument('action');
        $directory = $input->getArgument('directory');

        $dir = $this->shortcodeBuilder->create($name, $action, $directory);

        $output->writeln(sprintf('<info>Creating Shortcode in:</info> %s', $dir));
        $output->writeln(sprintf("<info> Shortcode {$action} name:</info> %s", $name));


        return Command::SUCCESS;
    }

}