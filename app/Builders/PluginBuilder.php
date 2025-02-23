<?php

namespace NikoGin\Builders;

use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class PluginBuilder
{
    public function __construct(private readonly BaseLogicGenerator $baseLogicGenerator, private readonly DirectoryService $directoryService)
    {}

    public function create(string $pluginName, string $pluginPrefix): string
    {

        $pluginDir = __DIR__ . '/../../../' . strtolower($pluginName);

        if (!mkdir($pluginDir, 0755, true) && !is_dir($pluginDir)) {
            throw new \RuntimeException('Failed to create plugin directory.');
        }
        $composerJson = $this->baseLogicGenerator->generateComposerJson($pluginName, $pluginPrefix);
        file_put_contents($pluginDir . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->createStructure($pluginDir, $pluginPrefix, $pluginName);

        return $pluginDir;
    }

    private function createStructure(string $pluginDir, string $pluginPrefix, string $pluginName): void
    {
        $directories = $this->directoryService->createDirectories($pluginDir);

        $files = [
            $pluginDir . "/" . strtolower($pluginName) . ".php" => $this->baseLogicGenerator->generateMainFileLogic($pluginPrefix, $pluginName),
            $directories['app'] . '/Plugin.php' => $this->baseLogicGenerator->generatePluginLogic($pluginPrefix, $pluginName),
            $directories['foundation'] . '/ProviderManager.php' => $this->baseLogicGenerator->generateProviderManagerLogic($pluginPrefix),
            $directories['foundation'] . '/ServiceProvider.php' => $this->baseLogicGenerator->generateServiceProviderLogic($pluginPrefix),
            $directories['foundation'] . '/DashboardController.php' => $this->baseLogicGenerator->generateDashboardControllerLogic($pluginPrefix),
            $directories['managers'] . '/ServiceProviderManager.php' => $this->baseLogicGenerator->generateServiceProviderManagerLogic($pluginPrefix),
            $directories['traits'] . '/IsSingleton.php' => $this->baseLogicGenerator->generateIsSingletonTraitLogic($pluginPrefix),
            $directories['support'] . '/Container.php' => $this->baseLogicGenerator->generateContainerLogic($pluginPrefix),
            $directories['support'] . '/Router.php' => $this->baseLogicGenerator->generateRouterLogic($pluginPrefix),
        ];

        foreach ($files as $path => $content) {
            file_put_contents($path, $content);
        }

    }
}