<?php

namespace NikoGin\Builders;

use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\ContractsLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;
use NikoGin\Services\Logic\BootLogicGenerator;

class PluginBuilder
{
    public function __construct(
        private readonly BaseLogicGenerator $baseLogicGenerator,
        private readonly DirectoryService $directoryService,
        private readonly ContractsLogicGenerator $contractsLogicGenerator,
        private readonly BootLogicGenerator $bootLogicGenerator,
    )
    {}

    public function create(string $pluginName, string $pluginPrefix, string $pluginDir, string $directorySlug): void
    {

        if (!mkdir($pluginDir, 0755, true) && !is_dir($pluginDir)) {
            throw new \RuntimeException('Failed to create plugin directory.');
        }
        $composerJson = $this->baseLogicGenerator->generateComposerJson($directorySlug, $pluginPrefix);
        file_put_contents($pluginDir . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->createStructure($pluginDir, $pluginPrefix, $pluginName, $directorySlug);

    }

    private function createStructure(string $pluginDir, string $pluginPrefix, string $pluginName, string $directorySlug): void
    {
        $directories = $this->directoryService->createDirectories($pluginDir);

        $files = [
            $pluginDir . "/" . $directorySlug . ".php"               => $this->baseLogicGenerator->generateMainFileLogic($pluginPrefix, $pluginName),
            $directories['contracts'] . '/Bootable.php'              => $this->contractsLogicGenerator->generateBootable($pluginPrefix),
            $directories['bootstrap'] . '/Activator.php'             => $this->bootLogicGenerator->generateActivator($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/Loader.php'                => $this->bootLogicGenerator->generateLoader($pluginPrefix),
            $directories['bootstrap'] . '/Deactivator.php'           => $this->bootLogicGenerator->generateDeactivator($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/Uninstaller.php'           => $this->bootLogicGenerator->generateUninstaller($pluginPrefix, $pluginName),
            $directories['app'] . '/Bootstrap.php'                   => $this->baseLogicGenerator->generateBootstrapLogic($pluginPrefix, $pluginName),
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
            $directories['routes'] . '/web.php'                      => $this->baseLogicGenerator->generateWebRouterLogic($pluginPrefix),
            $directories['routes'] . '/api.php'                      => $this->baseLogicGenerator->generateApiRouterLogic($pluginPrefix),
        ];

        foreach ($files as $path => $content) {
            file_put_contents($path, $content);
        }

    }
}