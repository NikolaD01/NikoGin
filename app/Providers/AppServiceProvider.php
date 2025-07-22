<?php

namespace NikoGin\Providers;

use NikoGin\Builders\ControllerBuilder;
use NikoGin\Builders\CronBuilder;
use NikoGin\Builders\ListenerBuilder;
use NikoGin\Builders\MiddlewareBuilder;
use NikoGin\Builders\MigrationBuilder;
use NikoGin\Builders\PluginBuilder;
use NikoGin\Builders\ProviderBuilder;
use NikoGin\Builders\RepositoryBuilder;
use NikoGin\Builders\ShortcodeBuilder;
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
    // TODO :: Clean up there is lot of unnecessary instances , this classes dont contain state
    protected array $services = [
        DirectoryService::class,

        //   BaseLogicGenerator::class,
        // BootLogicGenerator::class,
        //  ControllerLogicGenerator::class,
        //   MigrationLogicGenerator::class,
        ProviderLogicGenerator::class,
        //   ListenerLogicGenerator::class,
        // ContractsLogicGenerator::class,
        // CronLogicGenerator::class,
        RepositoryLogicGenerator::class,
        ShortcodeLogicGenerator::class,

        ProviderBuilder::class   => [],
        MigrationBuilder::class  => [],
        MiddlewareBuilder::class => [],
        ControllerBuilder::class => [DirectoryService::class],
        PluginBuilder::class     => [DirectoryService::class],
        ListenerBuilder::class   => [DirectoryService::class],
        CronBuilder::class       => [],
        RepositoryBuilder::class => [DirectoryService::class],
        ShortCodeBuilder::class  => [ShortcodeLogicGenerator::class],

        CreatePluginCommand::class     => [PluginBuilder::class],
        CreateControllerCommand::class => [ControllerBuilder::class],
        CreateMigrationCommand::class  => [MigrationBuilder::class],
        CreateProviderCommand::class   => [ProviderBuilder::class],
        CreateListenerCommand::class   => [ListenerBuilder::class],
        CreateCronCommand::class       => [CronBuilder::class],
        CreateRepositoryCommand::class => [RepositoryBuilder::class],
        CreateShortcodeCommand::class  => [ShortcodeBuilder::class],
        CreateMiddlewareCommand::class => [MiddlewareBuilder::class],
    ];
}