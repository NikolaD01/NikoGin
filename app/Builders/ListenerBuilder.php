<?php

namespace NikoGin\Builders;

use NikoGin\Command\CreateListenerCommand;
use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\ListenerLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class ListenerBuilder
{
    public function __construct(
        private  DirectoryService $directoryService)
    {}

    public function create(array $data): string
    {
        $currentDir = getcwd();

        $httpDir = sprintf('%s/%s/app/Http/', $currentDir, $data['dir']);

        $listenerDir = $this->directoryService->createListenerDirectory($httpDir);

        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($data['dir']);

        $data['dir'] = $listenerDir;

        ListenerLogicGenerator::generate($data, $pluginPrefix);

        return $listenerDir;
    }
}