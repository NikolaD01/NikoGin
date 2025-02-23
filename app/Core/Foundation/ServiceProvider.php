<?php

namespace NikoGin\Core\Foundation;

use NikoGin\Core\Support\Container;

abstract class ServiceProvider
{
    protected array $services = [];

    public function register(): void
    {
        foreach ($this->services as $service => $dependencies)
        {
            if (is_string($service) && is_array($dependencies))
            {
                Container::bind($service, function () use ($service, $dependencies)
                {
                    $resolvedDependencies = array_map(fn($dep) => Container::get($dep), $dependencies);
                    return new $service(...$resolvedDependencies);
                });
            } elseif (is_string($service))
            {
                Container::bind($service, function () use ($service, $dependencies)
                {
                    return new $service(Container::get($dependencies));
                });
            } else {
                Container::bind($dependencies, function () use ($dependencies)
                {
                    return new $dependencies();
                });
            }
        }
    }
}