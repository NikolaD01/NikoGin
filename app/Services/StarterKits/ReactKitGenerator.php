<?php

namespace NikoGin\Services\StarterKits;

use NikoGin\Services\StarterKits\Logic\BaseReactGenerator;
use NikoGin\Services\StarterKits\Logic\BlockGenerator;
use NikoGin\Services\StarterKits\Logic\ReactConfigGenerator;
use NikoGin\Services\StarterKits\Logic\ServicesGenerator;
use NikoGin\Services\StarterKits\Logic\TypesGenerator;

class ReactKitGenerator
{
    public static function generate(string $pluginDir, string $pluginPrefix, string $pluginName, array $directories): array
    {
        return [
            $pluginDir . '/package.json'                    => BaseReactGenerator::generatePackageJson($pluginName),
            $pluginDir . '/webpack.config.js'               => ReactConfigGenerator::generateWebpackConfig(),
            $pluginDir . '/tailwind.config.js'              => ReactConfigGenerator::generateTailwindConfig($pluginPrefix),
            $pluginDir . '/postcss.config.js'               => ReactConfigGenerator::generatePostcssConfig(),
            $pluginDir . '/tsconfig.json'                   => ReactConfigGenerator::generateTsconfig(),
            $directories['src'] . '/dashboard.tsx'          => BaseReactGenerator::generateDashboardTsx($pluginPrefix),
            $directories['src'] . '/block.ts'               => BlockGenerator::generateIndex(),
            $directories['pages'] . '/DashboardPage.tsx'    => BaseReactGenerator::generateDashboardPageTsx($pluginPrefix),
            $directories['styles'] . '/index.scss'           => BaseReactGenerator::generateTailwindIndexCss($pluginPrefix),
            $directories['block-example'] . '/block.json'   => BlockGenerator::blockExampleJson($pluginPrefix),
            $directories['block-example'] . '/index.tsx'    => BlockGenerator::blockExample(),
            $directories['block-example'] . '/index.scss'   => BlockGenerator::blockExampleScc(),
            $directories['types'] . '/block-props.d.ts'     => TypesGenerator::blockProps(),
            $directories['types'] . '/require-context.d.ts' => TypesGenerator::requireContext(),
            $directories['services'] . '/BaseApi.ts'        => ServicesGenerator::BaseApi($pluginPrefix),
            $directories['services'] . '/ExampleService.ts' => ServicesGenerator::generateExampleService(),
        ];
    }










}
