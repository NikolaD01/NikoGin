<?php

namespace NikoGin\Command;

use NikoGin\Core\Support\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'make:controller',
    description: 'Create a Controller for dashboard or Rest API'
)]
class CreateControllerCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the controller');
        $this->addArgument('type', InputArgument::REQUIRED, 'The type of the controller');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $type = strtolower($input->getArgument('type'));

        $helper = $this->getHelper('question');

        while (!in_array($type, Validator::ALLOWED_TYPES, true)) {
            $output->writeln('<error>Invalid type. Allowed types: rest, menu, submenu.</error>');

            $question = new ChoiceQuestion(
                'Please choose a valid type: ',
                Validator::ALLOWED_TYPES,
                0
            );
            $question->setErrorMessage('Invalid choice.');

            $type = $helper->ask($input, $output, $question);
        }



        $output->writeln(sprintf('<info>Creating Controller:</info> %s (%s)', $name, $type));

        return Command::SUCCESS;
    }

}