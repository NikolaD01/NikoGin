<?php
namespace NikoGin\Services\Logic;

class BootLogicGenerator
{
    public function generateActivator(string $pluginPrefix, string $pluginName): string
    {
        $constantDefinition = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))) . '_FILE';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;
use {$pluginPrefix}\\Core\\Managers\\ServiceProviderManager;

class Activator implements Bootable
{
    /**
     * Wire up WP activation hook.
     */
    public static function boot(): void
    {
        register_activation_hook(
            {$constantDefinition},
            [ self::class, 'run' ]
        );
    }

    /**
     * What happens on plugin activation.
     */
    public static function run(): void
    {
        // Register your service providers
        ServiceProviderManager::getInstance()->register();

        // Rebuild rewrite rules for CPTs, endpoints, etc.
        flush_rewrite_rules();
    }
}
PHP;
    }

    public function generateDeactivator(string $pluginPrefix, string $pluginName): string
    {
        $constantDefinition = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))) . '_FILE';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;

class Deactivator implements Bootable
{
    /**
     * Wire up WP deactivation hook.
     */
    public static function boot(): void
    {
        register_deactivation_hook(
            {$constantDefinition},
            [ self::class, 'run' ]
        );
    }

    /**
     * What happens on plugin deactivation.
     */
    public static function run(): void
    {
        flush_rewrite_rules();
    }
}
PHP;
    }

    public function generateLoader(string $pluginPrefix): string
    {
        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;
use {$pluginPrefix}\\Core\\Managers\\ServiceProviderManager;

class Loader implements Bootable
{
    /**
     * Wire up plugins_loaded hook.
     */
    public static function boot(): void
    {
        add_action(
            'plugins_loaded',
            [ self::class, 'run' ],
            20
        );
    }

    /**
     * What happens on every request after plugins are loaded.
     */
    public static function run(): void
    {
        ServiceProviderManager::getInstance()->register();
    }
}
PHP;
    }

    public function generateUninstaller(string $pluginPrefix, string $pluginName): string
    {
        $constantDefinition = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))) . '_FILE';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;

class Uninstaller implements Bootable
{
    /**
     * Wire up WP uninstall hook.
     */
    public static function boot(): void
    {
        register_uninstall_hook(
            {$constantDefinition},
            [ self::class, 'run' ]
        );
    }

    /**
     * What happens when plugin is uninstalled.
     */
    public static function run(): void
    {
        // Clean up database entries, options, transients, etc.
    }
}
PHP;
    }

    public function generateRoutesRegistrar(string $pluginPrefix, string $pluginName): string
    {
        $dirConstant = strtoupper(
                str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))
            ) . '_DIR';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;

class RoutesRegistrar implements Bootable
{
    /**
     * Hook into the REST API initialization.
     */
    public static function boot(): void
    {
        add_action('rest_api_init', [self::class, 'run']);
    }

    /**
     * Require every PHP file in app/routes/.
     */
    public static function run(): void
    {
        foreach (glob(self::PLUGIN_DIR . '/app/routes/*.php') as \$route_file) {
            require_once \$route_file;
        }
    }

    private const PLUGIN_DIR = {$dirConstant};
}
PHP;
    }
}