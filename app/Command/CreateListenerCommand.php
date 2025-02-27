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
    public function __construct(private ListenerBuilder $listenerBuilder)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the listener');
        $this->addArgument('action', InputArgument::REQUIRED, 'The action of the listener');
        $this->addArgument('directory', InputArgument::REQUIRED, 'The plugin directory (e.g., myplugin)');
        $this->addArgument('args', InputArgument::OPTIONAL, 'The number of args for callback');
        $this->addArgument('priority', InputArgument::OPTIONAL, 'The priority level of the listener');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $action = $input->getArgument('action');
        $directory = $input->getArgument('directory');
        $args = $input->getArgument('args');
        $priority = $input->getArgument('priority');

        $data = ['name'     => $name,
                 'action'   => $action,
                 'dir'      => $directory,
                 'args'     => $args,
                 'priority' => $priority];

        $dir = $this->listenerBuilder->create($data);


        return Command::SUCCESS;
    }
}