<?php

namespace NikoGin\Providers;

use NikoGin\Command\CreatePluginCommand;
use NikoGin\Core\Foundation\ServiceProvider;
use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\PluginCreatorService;
use NikoGin\Services\Structure\DirectoryService;

class AppServiceProvider extends ServiceProvider
{
    protected array $services = [
        DirectoryService::class,
        BaseLogicGenerator::class,
        PluginCreatorService::class => [BaseLogicGenerator::class, DirectoryService::class],
        CreatePluginCommand::class => [PluginCreatorService::class],
    ];
}