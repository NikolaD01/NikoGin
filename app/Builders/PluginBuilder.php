<?php

namespace NikoGin\Builders;

use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\ContractsLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;

class PluginBuilder
{
    public function __construct(
        private readonly BaseLogicGenerator $baseLogicGenerator,
        private readonly DirectoryService $directoryService,
        private readonly ContractsLogicGenerator $contractsLogicGenerator,
    )
    {}

    public function create(string $pluginName, string $pluginPrefix, string $pluginDir, string $directorySlug): void
    {

        if (!mkdir($pluginDir, 0755, true) && !is_dir($pluginDir)) {
            throw new \RuntimeException('Failed to create plugin directory.');
        }
        $composerJson = $this->baseLogicGenerator->generateComposerJson($pluginName, $pluginPrefix);
        file_put_contents($pluginDir . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->createStructure($pluginDir, $pluginPrefix, $pluginName, $directorySlug);

    }

    private function createStructure(string $pluginDir, string $pluginPrefix, string $pluginName, string $directorySlug): void
    {
        $directories = $this->directoryService->createDirectories($pluginDir);

        $files = [
            $pluginDir . "/" . $directorySlug . ".php"               => $this->baseLogicGenerator->generateMainFileLogic($pluginPrefix, $pluginName),
            $directories['app'] . '/Plugin.php'                      => $this->baseLogicGenerator->generatePluginLogic($pluginPrefix, $pluginName),
            $directories['foundation'] . '/ProviderManager.php'      => $this->baseLogicGenerator->generateProviderManagerLogic($pluginPrefix),
            $directories['foundation'] . '/ServiceProvider.php'      => $this->baseLogicGenerator->generateServiceProviderLogic($pluginPrefix),
            $directories['foundation'] . '/DashboardController.php'  => $this->baseLogicGenerator->generateDashboardControllerLogic($pluginPrefix),
            $directories['foundation'] . '/MenuController.php'       => $this->baseLogicGenerator->generateMenuControllerLogic($pluginPrefix),
            $directories['foundation'] . '/SubmenuController.php'    => $this->baseLogicGenerator->generateSubmenuControllerLogic($pluginPrefix),
            $directories['foundation'] . '/Migration.php'            => $this->baseLogicGenerator->generateMigrationLogic($pluginPrefix),
            $directories['foundation'] . '/Listener.php'             => $this->baseLogicGenerator->generateListenerLogic($pluginPrefix),
            $directories['foundation'] . '/Repository.php'           => $this->baseLogicGenerator->generateRepository($pluginPrefix),
            $directories['foundation'] . '/Shortcode.php'            => $this->baseLogicGenerator->generateShortcode($pluginPrefix),
            $directories['managers'] . '/ServiceProviderManager.php' => $this->baseLogicGenerator->generateServiceProviderManagerLogic($pluginPrefix),
            $directories['managers'] . '/ListenerManager.php'        => $this->baseLogicGenerator->generateListenerManagerLogic($pluginPrefix),
            $directories['traits'] . '/IsSingleton.php'              => $this->baseLogicGenerator->generateIsSingletonTraitLogic($pluginPrefix),
            $directories['traits'] . '/DB.php'                       => $this->baseLogicGenerator->generateDBLogic($pluginPrefix),
            $directories['support'] . '/Container.php'               => $this->baseLogicGenerator->generateContainerLogic($pluginPrefix),
            $directories['support'] . '/Router.php'                  => $this->baseLogicGenerator->generateRouterLogic($pluginPrefix),
            $directories['attributes'] . '/AsListener.php'           => $this->baseLogicGenerator->generateAsListenerLogic($pluginPrefix),
            $directories['contracts'] . '/CronInterface.php'         => $this->contractsLogicGenerator->generateCronInterface($pluginPrefix),
        ];

        foreach ($files as $path => $content) {
            file_put_contents($path, $content);
        }

    }
}