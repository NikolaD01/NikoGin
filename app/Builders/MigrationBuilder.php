<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\MigrationLogicGenerator;

class MigrationBuilder
{
    public function __construct()
    {}

    public function create(string $name, string $dir): string
    {

        $currentDir = getcwd();

        $migrationDir = sprintf('%s/%s/app/Http/Migrations', $currentDir, $dir);
        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        MigrationLogicGenerator::generate($name, $pluginPrefix, $migrationDir);

        return $dir;

    }
}