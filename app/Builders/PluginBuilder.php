<?php

namespace NikoGin\Builders;

use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\ContractsLogicGenerator;
use NikoGin\Services\Logic\MiddlewareLogicGenerator;
use NikoGin\Services\Structure\DirectoryService;
use NikoGin\Services\Logic\BootLogicGenerator;

class PluginBuilder
{
    public function __construct(
//        private readonly BaseLogicGenerator $baseLogicGenerator,
        private readonly DirectoryService $directoryService,
       // private readonly ContractsLogicGenerator $contractsLogicGenerator,
       // private readonly BootLogicGenerator $bootLogicGenerator,
    )
    {}

    public function create(string $pluginName, string $pluginPrefix, string $pluginDir, string $directorySlug): void
    {

        if (!mkdir($pluginDir, 0755, true) && !is_dir($pluginDir)) {
            throw new \RuntimeException('Failed to create plugin directory.');
        }
        $composerJson = BaseLogicGenerator::generateComposerJson($directorySlug, $pluginPrefix);
        file_put_contents($pluginDir . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->createStructure($pluginDir, $pluginPrefix, $pluginName, $directorySlug);

    }

    private function createStructure(string $pluginDir, string $pluginPrefix, string $pluginName, string $directorySlug): void
    {
        $directories = $this->directoryService->createDirectories($pluginDir);

        $files = [
            $pluginDir . "/" . $directorySlug . ".php"               => BaseLogicGenerator::generateMainFileLogic($pluginPrefix, $pluginName),
            $directories['contracts'] . '/Bootable.php'              => ContractsLogicGenerator::generateBootable($pluginPrefix),
            $directories['bootstrap'] . '/Activator.php'             => BootLogicGenerator::generateActivator($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/Loader.php'                => BootLogicGenerator::generateLoader($pluginPrefix),
            $directories['bootstrap'] . '/Deactivator.php'           => BootLogicGenerator::generateDeactivator($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/Uninstaller.php'           => BootLogicGenerator::generateUninstaller($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/RoutesRegistrar.php'       => BootLogicGenerator::generateRoutesRegistrar($pluginPrefix, $pluginName),
            $directories['app'] . '/Bootstrap.php'                   => BaseLogicGenerator::generateBootstrapLogic($pluginPrefix, $pluginName),
            $directories['foundation'] . '/ProviderManager.php'      => BaseLogicGenerator::generateProviderManagerLogic($pluginPrefix),
            $directories['foundation'] . '/ServiceProvider.php'      => BaseLogicGenerator::generateServiceProviderLogic($pluginPrefix),
            $directories['foundation'] . '/DashboardController.php'  => BaseLogicGenerator::generateDashboardControllerLogic($pluginPrefix),
            $directories['foundation'] . '/MenuController.php'       => BaseLogicGenerator::generateMenuControllerLogic($pluginPrefix),
            $directories['foundation'] . '/SubmenuController.php'    => BaseLogicGenerator::generateSubmenuControllerLogic($pluginPrefix),
            $directories['foundation'] . '/Migration.php'            => BaseLogicGenerator::generateMigrationLogic($pluginPrefix),
            $directories['foundation'] . '/Listener.php'             => BaseLogicGenerator::generateListenerLogic($pluginPrefix),
            $directories['foundation'] . '/Repository.php'           => BaseLogicGenerator::generateRepository($pluginPrefix),
            $directories['foundation'] . '/Shortcode.php'            => BaseLogicGenerator::generateShortcode($pluginPrefix),
            $directories['managers'] . '/ServiceProviderManager.php' => BaseLogicGenerator::generateServiceProviderManagerLogic($pluginPrefix),
            $directories['managers'] . '/ListenerManager.php'        => BaseLogicGenerator::generateListenerManagerLogic($pluginPrefix),
            $directories['traits'] . '/IsSingleton.php'              => BaseLogicGenerator::generateIsSingletonTraitLogic($pluginPrefix),
            $directories['traits'] . '/DB.php'                       => BaseLogicGenerator::generateDBLogic($pluginPrefix),
            $directories['support'] . '/Container.php'               => BaseLogicGenerator::generateContainerLogic($pluginPrefix),
            $directories['support'] . '/Router.php'                  => BaseLogicGenerator::generateRouterLogic($pluginPrefix),
            $directories['support'] . '/HTTP.php'                    => BaseLogicGenerator::generateHTTPLogic($pluginPrefix),
            $directories['attributes'] . '/AsListener.php'           => BaseLogicGenerator::generateAsListenerLogic($pluginPrefix),
            $directories['contracts'] . '/CronInterface.php'         => ContractsLogicGenerator::generateCronInterface($pluginPrefix),
            $directories['contracts'] . '/MiddlewareInterface.php'   => ContractsLogicGenerator::generateMiddlewareInterface($pluginPrefix),
            $directories['middlewares'] . '/BasicAuth.php'           => MiddlewareLogicGenerator::generateBasicAuth($pluginPrefix),
            $directories['middlewares'] . '/BearerTokenAuth.php'     => MiddlewareLogicGenerator::generateBearerTokenAuth($pluginPrefix),
            $directories['routes'] . '/web.php'                      => BaseLogicGenerator::generateWebRouterLogic($pluginPrefix),
            $directories['routes'] . '/api.php'                      => BaseLogicGenerator::generateApiRouterLogic($pluginPrefix),
        ];

        foreach ($files as $path => $content) {
            file_put_contents($path, $content);
        }

    }
}