<?php

namespace NikoGin\Core\Foundation;

abstract class ProviderManager
{

    protected array $providers = [
    ];


    public function register(): void
    {
        foreach ($this->providers as $providerClass) {
            $provider = new $providerClass();
            $provider->register();
        }
    }
}