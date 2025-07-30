<?php

namespace NikoGin\Builders;

use NikoGin\Services\Logic\AttributesLogicGenerator;
use NikoGin\Services\Logic\BaseLogicGenerator;
use NikoGin\Services\Logic\ContractsLogicGenerator;
use NikoGin\Services\Logic\FileLogicGenerator;
use NikoGin\Services\Logic\ManagerLogicGenerator;
use NikoGin\Services\Logic\MiddlewareLogicGenerator;
use NikoGin\Services\Logic\SupportLogicGenerator;
use NikoGin\Services\Logic\TraitLogicGenerator;
use NikoGin\Services\StarterKits\ReactKitGenerator;
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

    public function create(string $pluginName, string $pluginPrefix, string $pluginDir, string $directorySlug, array $options = []): void
    {

        if (!mkdir($pluginDir, 0755, true) && !is_dir($pluginDir)) {
            throw new \RuntimeException('Failed to create plugin directory.');
        }
        $composerJson = BaseLogicGenerator::generateComposerJson($directorySlug, $pluginPrefix);
        file_put_contents($pluginDir . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->createStructure($pluginDir, $pluginPrefix, $pluginName, $directorySlug, $options);

    }

    private function createStructure(string $pluginDir, string $pluginPrefix, string $pluginName, string $directorySlug, array $options): void
    {

        $directories = $this->directoryService->createDirectories($pluginDir);

        $starterKit = $options['kit'] ?? '';

        $files = [
            $pluginDir . "/" . $directorySlug . ".php"               => FileLogicGenerator::generateMainFileLogic($pluginPrefix, $pluginName),
            $directories['contracts'] . '/Bootable.php'              => ContractsLogicGenerator::generateBootable($pluginPrefix),
            $directories['bootstrap'] . '/Activator.php'             => BootLogicGenerator::generateActivator($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/Loader.php'                => BootLogicGenerator::generateLoader($pluginPrefix),
            $directories['bootstrap'] . '/Deactivator.php'           => BootLogicGenerator::generateDeactivator($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/Uninstaller.php'           => BootLogicGenerator::generateUninstaller($pluginPrefix, $pluginName),
            $directories['bootstrap'] . '/BlockRegistrar.php'        => BootLogicGenerator::generateBlockRegistrar($pluginPrefix, $pluginName),
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
            $directories['managers'] . '/ServiceProviderManager.php' => ManagerLogicGenerator::generateServiceProviderManagerLogic($pluginPrefix),
            $directories['managers'] . '/ListenerManager.php'        => ManagerLogicGenerator::generateListenerManagerLogic($pluginPrefix),
            $directories['traits'] . '/IsSingleton.php'              => TraitLogicGenerator::generateIsSingletonTraitLogic($pluginPrefix),
            $directories['traits'] . '/DB.php'                       => TraitLogicGenerator::generateDBLogic($pluginPrefix),
            $directories['traits'] . '/HasPermissions.php'               => TraitLogicGenerator::generateHasPermissionsTrait($pluginPrefix),
            $directories['support'] . '/Container.php'               => SupportLogicGenerator::generateContainerLogic($pluginPrefix),
            $directories['support'] . '/Router.php'                  => SupportLogicGenerator::generateRouterLogic($pluginPrefix, $pluginName),
            $directories['support'] . '/HTTP.php'                    => SupportLogicGenerator::generateHTTPLogic($pluginPrefix),
            $directories['attributes'] . '/AsListener.php'           => AttributesLogicGenerator::generateAsListenerLogic($pluginPrefix),
            $directories['contracts'] . '/CronInterface.php'         => ContractsLogicGenerator::generateCronInterface($pluginPrefix),
            $directories['contracts'] . '/MiddlewareInterface.php'   => ContractsLogicGenerator::generateMiddlewareInterface($pluginPrefix),
            $directories['middlewares'] . '/BasicAuth.php'           => MiddlewareLogicGenerator::generateBasicAuth($pluginPrefix),
            $directories['middlewares'] . '/BearerTokenAuth.php'     => MiddlewareLogicGenerator::generateBearerTokenAuth($pluginPrefix),
            $directories['routes'] . '/web.php'                      => FileLogicGenerator::generateWebRouterLogic($pluginPrefix),
            $directories['routes'] . '/api.php'                      => FileLogicGenerator::generateApiRouterLogic($pluginPrefix),
        ];

        if ($starterKit === 'React') {
            $reactFiles = ReactKitGenerator::generate($pluginDir, $pluginPrefix, $pluginName);

            $srcDir = $this->directoryService->createDir($pluginDir, 'src');
            $this->directoryService->createDir($srcDir , 'pages');
            $this->directoryService->createDir($srcDir , 'styles');
            $this->directoryService->createDir($srcDir , 'types');
            $blockDir = $this->directoryService->createDir($srcDir , 'blocks');
            $this->directoryService->createDir($blockDir , 'block-example');


            $files = array_merge($files, $reactFiles);
        }


        foreach ($files as $path => $content) {
            file_put_contents($path, $content);
        }

    }
}