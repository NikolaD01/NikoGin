<?php

namespace NikoGin\Command;

use NikoGin\Builders\MigrationBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'make:cron',
    description: 'Create wordpress cron'
)]
class CreateCronCommand extends Command
{
    public function __construct()
    {}

    protected function configure() : void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
        $this->addArgument('directory', InputArgument::REQUIRED);

    }
}