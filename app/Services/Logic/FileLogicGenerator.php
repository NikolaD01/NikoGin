<?php

namespace NikoGin\Services\Logic;

class FileLogicGenerator
{
    public static function generateMainFileLogic(string $pluginPrefix, string $pluginName): string
    {
        // Generate the constant base name from plugin name
        $constantBase = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName)));

        $constantFile = "{$constantBase}_FILE";
        $constantDir = "{$constantBase}_DIR";
        $constantUrl = "{$constantBase}_URL";
        $constantNamespace = "{$constantBase}_NAMESPACE";
        $constantVersion = "{$constantBase}_VERSION";
        $constantSlug = "{$constantBase}_SLUG";

        // Define slug and namespace values
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $pluginName));
        $namespace = strtolower($pluginPrefix) . '/v1';

        return "<?php

/**
 * Plugin Name: {$pluginName}
 * Description: A new WordPress plugin.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: MIT
 */

// Define constants
if ( ! defined( '{$constantFile}' ) ) {
    define( '{$constantFile}', __FILE__ );
}

if ( ! defined( '{$constantDir}' ) ) {
    define( '{$constantDir}', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( '{$constantUrl}' ) ) {
    define( '{$constantUrl}', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( '{$constantNamespace}' ) ) {
    define( '{$constantNamespace}', '{$namespace}' );
}

if ( ! defined( '{$constantVersion}' ) ) {
    define( '{$constantVersion}', '1.0.0' );
}

if ( ! defined( '{$constantSlug}' ) ) {
    define( '{$constantSlug}', '{$slug}' );
}

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';


// We need to manually define package/plugin file to load it inside our plugin
// this package also exist as separate wp plugin 
if (class_exists('ActionScheduler') === false) {
    require_once PWI_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
}
// Instantiate the Plugin class
use {$pluginPrefix}\\Bootstrap;
Bootstrap::init();";
    }

    public static function generateWebRouterLogic(string $pluginPrefix): string
    {
        // TODO: think what to do here
        return "<?php";
    }

    public static function generateApiRouterLogic(string $pluginPrefix): string
    {
        return "<?php\n\nuse {$pluginPrefix}\\Core\\Support\\Container;\n\nuse {$pluginPrefix}\\Core\\Support\\Router;";
    }

}