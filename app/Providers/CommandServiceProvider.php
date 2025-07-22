<?php

namespace NikoGin\Providers;

use Exception;
use NikoGin\Command\CreateControllerCommand;
use NikoGin\Command\CreateCronCommand;
use NikoGin\Command\CreateListenerCommand;
use NikoGin\Command\CreateMiddlewareCommand;
use NikoGin\Command\CreateMigrationCommand;
use NikoGin\Command\CreatePluginCommand;
use NikoGin\Command\CreateProviderCommand;
use NikoGin\Command\CreateRepositoryCommand;
use NikoGin\Command\CreateShortcodeCommand;
use NikoGin\Core\Foundation\ServiceProvider;
use NikoGin\Core\Support\Container;
use Symfony\Component\Console\Application;

class CommandServiceProvider extends ServiceProvider
{
    protected array $services = [
        CreatePluginCommand::class,
        CreateControllerCommand::class,
        CreateMigrationCommand::class,
        CreateProviderCommand::class,
        CreateListenerCommand::class,
        CreateCronCommand::class,
        CreateRepositoryCommand::class,
        CreateShortcodeCommand::class,
        CreateMiddlewareCommand::class
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