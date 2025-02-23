<?php

namespace NikoGin\Providers;

use NikoGin\Builders\ControllerBuilder;
use NikoGin\Builders\PluginBuilder;
use NikoGin\Command\CreateControllerCommand;
use NikoGin\Command\CreatePluginCommand;
use NikoGin\Core\Foundation\ServiceProvider;
use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\ControllerLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class AppServiceProvider extends ServiceProvider
{
    protected array $services = [
        DirectoryService::class,
        BaseLogicGenerator::class,
        ControllerLogicGenerator::class,
        ControllerBuilder::class => [DirectoryService::class, ControllerLogicGenerator::class],
        PluginBuilder::class => [BaseLogicGenerator::class, DirectoryService::class],
        CreatePluginCommand::class => [PluginBuilder::class],
        CreateControllerCommand::class => [ControllerBuilder::class],
    ];
}