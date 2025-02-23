<?php

namespace NikoGin\Services;

use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class PluginCreatorService
{
    public function __construct(private readonly BaseLogicGenerator $baseLogicGenerator, private readonly DirectoryService $directoryService)
    {}

    public function create(string $pluginName, string $pluginPrefix): string
    {

        $pluginDir = __DIR__ . '/../../../' . strtolower($pluginName);

        // Create the main plugin directory
        if (!mkdir($pluginDir, 0755, true) && !is_dir($pluginDir)) {
            throw new \RuntimeException('Failed to create plugin directory.');
        }
        // Create subdirectories
        $this->createStructure($pluginDir, $pluginPrefix, $pluginName);

        // Prepare the composer.json content
        $composerJson = $this->baseLogicGenerator->generateComposerJson($pluginName, $pluginPrefix);
        // Create the composer.json file
        file_put_contents($pluginDir . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $pluginDir;
    }

    private function createStructure(string $pluginDir, string $pluginPrefix, string $pluginName): void
    {
        $directories = $this->directoryService->createDirectories($pluginDir);

        file_put_contents($pluginDir . "/".strtolower($pluginName ).".php", $this->baseLogicGenerator->generateMainFileLogic($pluginPrefix, $pluginName));
        file_put_contents($directories['app'] . '/Plugin.php', $this->baseLogicGenerator->generatePluginLogic($pluginPrefix, $pluginName));
        file_put_contents($directories['foundation'] . '/ProviderManager.php', $this->baseLogicGenerator->generateProviderManagerLogic($pluginPrefix));
        file_put_contents($directories['foundation'] . '/ServiceProvider.php', $this->baseLogicGenerator->generateServiceProviderLogic($pluginPrefix));
        file_put_contents($directories['managers'] . '/ServiceProviderManager.php', $this->baseLogicGenerator->generateServiceProviderManagerLogic($pluginPrefix));
        file_put_contents($directories['traits'] . '/IsSingleton.php', $this->baseLogicGenerator->generateIsSingletonTraitLogic($pluginPrefix));
        file_put_contents($directories['support'] . '/Container.php', $this->baseLogicGenerator->generateContainerLogic($pluginPrefix));
        file_put_contents($directories['support'] . '/Router.php', $this->baseLogicGenerator->generateRouterLogic($pluginPrefix));
    }
}