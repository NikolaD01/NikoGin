<?php

namespace NikoGin\Providers;

use NikoGin\Builders\ControllerBuilder;
use NikoGin\Builders\ListenerBuilder;
use NikoGin\Builders\MigrationBuilder;
use NikoGin\Builders\PluginBuilder;
use NikoGin\Builders\ProviderBuilder;
use NikoGin\Command\CreateControllerCommand;
use NikoGin\Command\CreateListenerCommand;
use NikoGin\Command\CreateMigrationCommand;
use NikoGin\Command\CreatePluginCommand;
use NikoGin\Command\CreateProviderCommand;
use NikoGin\Core\Foundation\ServiceProvider;
use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\ContractsLogicGenerator;
use NikoGin\Services\Logic\ControllerLogicGenerator;
use NikoGin\Services\Logic\ListenerLogicGenerator;
use NikoGin\Services\Logic\MigrationLogicGenerator;
use NikoGin\Services\Logic\ProviderLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class AppServiceProvider extends ServiceProvider
{
    protected array $services = [
        DirectoryService::class,

        BaseLogicGenerator::class,
        ControllerLogicGenerator::class,
        MigrationLogicGenerator::class,
        ProviderLogicGenerator::class,
        ListenerLogicGenerator::class,
        ContractsLogicGenerator::class,


        ProviderBuilder::class         => [ProviderLogicGenerator::class],
        MigrationBuilder::class        => [MigrationLogicGenerator::class],
        ControllerBuilder::class       => [DirectoryService::class, ControllerLogicGenerator::class],
        PluginBuilder::class           => [BaseLogicGenerator::class, DirectoryService::class, ContractsLogicGenerator::class],
        ListenerBuilder::class         => [ListenerLogicGenerator::class, DirectoryService::class],

        CreatePluginCommand::class     => [PluginBuilder::class],
        CreateControllerCommand::class => [ControllerBuilder::class],
        CreateMigrationCommand::class  => [MigrationBuilder::class],
        CreateProviderCommand::class   => [ProviderBuilder::class],
        CreateListenerCommand::class   => [ListenerBuilder::class],
    ];
}