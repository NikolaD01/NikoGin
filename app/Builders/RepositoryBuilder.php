<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\RepositoryLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class RepositoryBuilder
{
    public function __construct(private readonly DirectoryService $directoryService, private RepositoryLogicGenerator $repoLogicGenerator)
    {}

    public function create(string $name, string $table, string $dir): string
    {

        $currentDir = getcwd();
        $httpDir = sprintf('%s/%s/app/Http/', $currentDir, $dir);
        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        $dir = $this->directoryService->createDir($httpDir, 'Repositories');
        $this->repoLogicGenerator->generate($name, $table ,$dir, $pluginPrefix);

        return $dir;

    }
}