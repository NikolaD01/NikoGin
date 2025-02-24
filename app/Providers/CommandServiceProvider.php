<?php

namespace NikoGin\Providers;

use Exception;
use NikoGin\Command\CreateControllerCommand;
use NikoGin\Command\CreateMigrationCommand;
use NikoGin\Command\CreatePluginCommand;
use NikoGin\Core\Foundation\ServiceProvider;
use NikoGin\Core\Support\Container;
use Symfony\Component\Console\Application;

class CommandServiceProvider extends ServiceProvider
{
    protected array $services = [
        CreatePluginCommand::class,
        CreateControllerCommand::class,
        CreateMigrationCommand::class,
    ];

    public function register(): void
    {
        $app = new Application();
        foreach ($this->services as $service) {
            $app->add(Container::get($service));
        }
        try {
            $app->run();
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }
}