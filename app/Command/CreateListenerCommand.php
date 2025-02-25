<?php

namespace NikoGin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'make:listener',
    description: 'Create a Wordpress listener'
)]
class CreateListenerCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the listener');
        $this->addArgument('action', InputArgument::REQUIRED, 'The action of the listener');
        $this->addArgument('directory', InputArgument::OPTIONAL, 'The plugin directory (e.g., myplugin)');
        $this->addArgument('args', InputArgument::OPTIONAL, 'The number of args for callback');
        $this->addArgument('priority', InputArgument::OPTIONAL, 'The priority level of the listener');
    }
}