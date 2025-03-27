<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\CronLogicGenerator;

class CronBuilder
{
    public function __construct(public CronLogicGenerator $cronLogicGenerator)
    {}

    public function create(string $name, string $dir) : string
    {

        $currentDir = getcwd();

        $cronDir = sprintf('%s/%s/app/Http/Crons', $currentDir, $dir);
        $providerDir = sprintf('%s/%s/app/Http/Providers', $currentDir, $dir);

        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        $this->cronLogicGenerator->generateProvider($name, $pluginPrefix, $providerDir);
        $this->cronLogicGenerator->generate($name, $pluginPrefix, $cronDir);

        return $dir;
    }

}