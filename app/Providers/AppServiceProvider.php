<?php

namespace NikoGin\Providers;

use NikoGin\Builders\ControllerBuilder;
use NikoGin\Builders\CronBuilder;
use NikoGin\Builders\ListenerBuilder;
use NikoGin\Builders\MigrationBuilder;
use NikoGin\Builders\PluginBuilder;
use NikoGin\Builders\ProviderBuilder;
use NikoGin\Builders\RepositoryBuilder;
use NikoGin\Builders\ShortcodeBuilder;
use NikoGin\Command\CreateControllerCommand;
use NikoGin\Command\CreateCronCommand;
use NikoGin\Command\CreateListenerCommand;
use NikoGin\Command\CreateMigrationCommand;
use NikoGin\Command\CreatePluginCommand;
use NikoGin\Command\CreateProviderCommand;
use NikoGin\Command\CreateRepositoryCommand;
use NikoGin\Command\CreateShortcodeCommand;
use NikoGin\Core\Foundation\ServiceProvider;
use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\BootLogicGenerator;
use NikoGin\Services\Logic\ContractsLogicGenerator;
use NikoGin\Services\Logic\ControllerLogicGenerator;
use NikoGin\Services\Logic\CronLogicGenerator;
use NikoGin\Services\Logic\ListenerLogicGenerator;
use NikoGin\Services\Logic\MigrationLogicGenerator;
use NikoGin\Services\Logic\ProviderLogicGenerator;
use NikoGin\Services\Logic\RepositoryLogicGenerator;
use NikoGin\Services\Logic\ShortcodeLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class AppServiceProvider extends ServiceProvider
{
    protected array $services = [
        DirectoryService::class,

        BaseLogicGenerator::class,
        BootLogicGenerator::class,
        ControllerLogicGenerator::class,
        MigrationLogicGenerator::class,
        ProviderLogicGenerator::class,
        ListenerLogicGenerator::class,
        ContractsLogicGenerator::class,
        CronLogicGenerator::class,
        RepositoryLogicGenerator::class,
        ShortcodeLogicGenerator::class,

        ProviderBuilder::class   => [ProviderLogicGenerator::class],
        MigrationBuilder::class  => [MigrationLogicGenerator::class],
        ControllerBuilder::class => [DirectoryService::class, ControllerLogicGenerator::class],
        PluginBuilder::class     => [BaseLogicGenerator::class, DirectoryService::class, ContractsLogicGenerator::class, BootLogicGenerator::class],
        ListenerBuilder::class   => [ListenerLogicGenerator::class, DirectoryService::class],
        CronBuilder::class => [CronLogicGenerator::class],
        RepositoryBuilder::class => [DirectoryService::class,RepositoryLogicGenerator::class],
        ShortCodeBuilder::class => [ShortcodeLogicGenerator::class],

        CreatePluginCommand::class     => [PluginBuilder::class],
        CreateControllerCommand::class => [ControllerBuilder::class],
        CreateMigrationCommand::class  => [MigrationBuilder::class],
        CreateProviderCommand::class   => [ProviderBuilder::class],
        CreateListenerCommand::class   => [ListenerBuilder::class],
        CreateCronCommand::class => [CronBuilder::class],
        CreateRepositoryCommand::class => [RepositoryBuilder::class],
        CreateShortcodeCommand::class => [ShortcodeBuilder::class],
    ];
}