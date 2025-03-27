<?php

namespace NikoGin\Command;

use NikoGin\Builders\ListenerBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:listener',
    description: 'Create a Wordpress listener'
)]
class CreateListenerCommand extends Command
{
    public function __construct(private readonly ListenerBuilder $listenerBuilder)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the listener');
        $this->addArgument('action', InputArgument::REQUIRED, 'The action of the listener');
        $this->addArgument('directory', InputArgument::REQUIRED, 'The plugin directory (e.g., myplugin)');
        $this->addArgument('type', InputArgument::OPTIONAL, 'The type of the listener action/filter');
        $this->addArgument('args', InputArgument::OPTIONAL, 'The number of args for callback');
        $this->addArgument('priority', InputArgument::OPTIONAL, 'The priority level of the listener');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $action = $input->getArgument('action');
        $directory = $input->getArgument('directory');
        $type = $input->getArgument('type');
        $args = $input->getArgument('args');
        $priority = $input->getArgument('priority');

        if($type !== "action" && $type !== "filter")
        {
            $output->writeln('<error>Invalid type</error>');
            return Command::FAILURE;
        }

        $data = ['name'     => $name,
                 'listener'   => $action,
                 'dir'      => $directory,
                 'type'     => $type,
                 'args'     => $args,
                 'priority' => $priority];

        $dir = $this->listenerBuilder->create($data);

        $output->writeln(sprintf('<info>Creating Listener in:</info> %s', $dir));
        $output->writeln(sprintf("<info> Listener {$type} name:</info> %s", $name));


        return Command::SUCCESS;
    }
}