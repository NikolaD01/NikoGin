<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\ControllerLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class ControllerBuilder
{
    public function __construct(private readonly DirectoryService $directoryService, private ControllerLogicGenerator $controllerLogicGenerator)
    {}

    public function create(string $name, string $type, string $dir): string
    {

        $currentDir = getcwd();
        $controllersDir = sprintf('%s/%s/app/Http/Controllers', $currentDir, $dir);
        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        $dir = $this->directoryService->createControllerDirectories($controllersDir, $type);
        $this->controllerLogicGenerator->generate($name, $type ,$dir, $pluginPrefix);

        return $dir;

    }
}