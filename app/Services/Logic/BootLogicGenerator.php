<?php
namespace NikoGin\Services\Logic;

class BootLogicGenerator
{
    public static function generateActivator(string $pluginPrefix, string $pluginName): string
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

    public static function generateDeactivator(string $pluginPrefix, string $pluginName): string
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

    public static function generateLoader(string $pluginPrefix): string
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

    public static function generateUninstaller(string $pluginPrefix, string $pluginName): string
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

    public static function generateRoutesRegistrar(string $pluginPrefix, string $pluginName): string
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

    public static function generateBlockRegistrar(string $pluginPrefix, string $pluginName): string
    {
        $dirConstant = strtoupper(
                str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))
            ) . '_DIR';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;

class BlockRegistrar implements Bootable
{
    public static function boot(): void
    {
        add_action('init', [self::class, 'run']);
    }

    public static function run(): void
    {
        \$blocksDir = {$dirConstant} . '/build/blocks';
        \$blockFolders = array_filter(glob(\$blocksDir . '/*'), 'is_dir');

        foreach (\$blockFolders as \$blockPath) {
            \$blockName = basename(\$blockPath);
            \$styleHandle = "{$pluginPrefix}-{\$blockName}-style";
            \$editorStyleHandle = "{$pluginPrefix}-{\$blockName}-editor-style";

            \$stylePath = "\$blockPath/style.css";
            if (file_exists(\$stylePath)) {
                wp_register_style(
                    \$styleHandle,
                    {$dirConstant} . "/build/blocks/{\$blockName}/style.css",
                    [],
                    filemtime(\$stylePath)
                );
            }

            \$editorStylePath = "\$blockPath/editor.css";
            if (file_exists(\$editorStylePath)) {
                wp_register_style(
                    \$editorStyleHandle,
                    {$dirConstant} . "/build/blocks/{\$blockName}/editor.css",
                    ['wp-edit-blocks'],
                    filemtime(\$editorStylePath)
                );
            }

            \$blockJsonPath = \$blockPath . '/block.json';
            if (file_exists(\$blockJsonPath)) {
                register_block_type(\$blockPath, [
                    'style' => file_exists(\$stylePath) ? \$styleHandle : null,
                    'editor_style' => file_exists(\$editorStylePath) ? \$editorStyleHandle : null,
                ]);
            }
        }
    }
}
PHP;
    }


    public static function generateAssetsRegistrar(string $pluginPrefix, string $pluginName): string
    {
        $dirConstant = strtoupper(
                str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))
            ) . '_DIR';

        $urlConstant = strtoupper(
                str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))
            ) . '_URL';

        $namespaceConstant = strtoupper(
                str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))
            ) . '_NAMESPACE';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Bootstrap;

use {$pluginPrefix}\\Core\\Contracts\\Bootable;

class AssetsRegistrar implements Bootable
{
    private static array \$pages = [
    ];

    public static function boot(): void
    {
        add_action('admin_enqueue_scripts', [self::class, 'run']);
    }

    public static function run(): void
    {
        if (!isset(\$_GET['page'])) {
            return;
        }

        \$currentPage = sanitize_text_field(\$_GET['page']);

        if (isset(self::\$pages[\$currentPage])) {
            \$handle = \$currentPage;
            \$scriptName = self::\$pages[\$currentPage];
            \$basePath = {$dirConstant} . "build/{\$scriptName}";
            \$baseUrl  = {$urlConstant} . "build/{\$scriptName}";

            \$scriptSource = \$baseUrl . ".js";
            wp_enqueue_script(
                \$handle,
                \$scriptSource,
                ['wp-element', 'wp-components', 'wp-api-fetch'],
                file_exists("\{\$basePath}.js") ? filemtime("\{\$basePath}.js") : '1.0.0',
                true
            );

            wp_localize_script(\$handle, '{$pluginName}', [
                'apiUrl'    => rest_url({$namespaceConstant}),
                'nonce'     => wp_create_nonce('wp_rest'),
                'adminUrl'  => admin_url(),
                'pluginUrl' => {$urlConstant},
            ]);

            \$stylePath = "\{\$basePath}.css";
            if (file_exists(\$stylePath)) {
                wp_enqueue_style(
                    \$handle,
                    \$baseUrl . ".css",
                    [],
                    filemtime(\$stylePath)
                );
            }
        }
    }
}
PHP;
    }

}