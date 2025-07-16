<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\MiddlewareLogicGenerator;

class MiddlewareBuilder
{

    public function create(mixed $name, mixed $dir): string
    {
        $currentDir = getcwd();

        $middlewareDir = sprintf('%s/%s/app/Http/Middlewares', $currentDir, $dir);
        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);
        MiddlewareLogicGenerator::generate($name, $pluginPrefix, $middlewareDir);

        return $dir;
    }
}