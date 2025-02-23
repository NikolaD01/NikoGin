<?php

namespace NikoGin\Services\Structure;

class DirectoryService
{
    private array $directories = [];

    public function createDirectories(string $pluginDir): array
    {
        // Define the app directory
        $appDir = $pluginDir . '/app';
        mkdir($appDir, 0755);
        $this->directories['app'] = $appDir;

        // Create Core directory
        $coreDir = $appDir . '/Core';
        mkdir($coreDir, 0755);
        $this->directories['core'] = $coreDir;

        // Create Foundation directory
        $foundationDir = $coreDir . '/Foundation';
        mkdir($foundationDir, 0755);
        $this->directories['foundation'] = $foundationDir;

        // Create Support directory
        $supportDir = $coreDir . '/Support';
        mkdir($supportDir, 0755);
        $this->directories['support'] = $supportDir;

        // Create Managers directory
        $managersDir = $coreDir . '/Managers';
        mkdir($managersDir, 0755);
        $this->directories['managers'] = $managersDir;

        // Create Traits directory
        $traitsDir = $supportDir . '/Traits';
        mkdir($traitsDir, 0755);
        $this->directories['traits'] = $traitsDir;

        // Create Http directory
        $httpDir = $appDir . '/Http';
        mkdir($httpDir, 0755);
        $this->directories['http'] = $httpDir;

        // Create Controllers directory
        $controllerDir = $httpDir . '/Controllers';
        mkdir($controllerDir, 0755);
        $this->directories['controllers'] = $controllerDir;

        // Create Providers directory
        $providerDir = $httpDir . '/Providers';
        mkdir($providerDir, 0755);
        $this->directories['providers'] = $providerDir;

        // Create Migrations directory
        $migrationDir = $httpDir . '/Migrations';
        mkdir($migrationDir, 0755);
        $this->directories['migrations'] = $migrationDir;

        // Create Seeders directory
        $seederDir = $migrationDir . '/Seeders';
        mkdir($seederDir, 0755);
        $this->directories['seeders'] = $seederDir;

        return $this->directories;
    }
}