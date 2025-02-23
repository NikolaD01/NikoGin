<?php

namespace NikoGin\Managers;


use NikoGin\Core\Foundation\ProviderManager;
use NikoGin\Core\Support\Traits\IsSingleton;
use NikoGin\Providers\AppServiceProvider;
use NikoGin\Providers\CommandServiceProvider;

class ServiceProviderManager extends ProviderManager
{
    use IsSingleton;


    protected array $providers = [
        AppServiceProvider::class,
        CommandServiceProvider::class,
    ];
}