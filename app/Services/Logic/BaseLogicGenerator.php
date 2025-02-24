<?php

namespace NikoGin\Services\Logic;

class BaseLogicGenerator
{

    public function generateMainFileLogic(string $pluginPrefix, string $pluginName): string
    {
        // Generate the constant names
        $constantName = strtoupper($pluginName);
        $constantFile = "{$constantName}_FILE";
        $constantDir = "{$constantName}_DIR";
        $constantUrl = "{$constantName}_URL";

        return "<?php\n\n/**
 * Plugin Name: {$pluginName}
 * Description: A new WordPress plugin.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: MIT
 */\n\n// Define constants\nif ( ! defined( '{$constantFile}' ) ) {\n    define( '{$constantFile}', __FILE__ );\n}\n\nif ( ! defined( '{$constantDir}' ) ) {\n    define( '{$constantDir}', plugin_dir_path( __FILE__ ) );\n}\n\nif ( ! defined( '{$constantUrl}' ) ) {\n    define( '{$constantUrl}', plugin_dir_url( __FILE__ ) );\n}\n\n// Load the Composer autoloader\nrequire_once __DIR__ . '/vendor/autoload.php';\n\n// Instantiate the Plugin class\nuse {$pluginPrefix}\\Plugin;\nnew Plugin();";
    }
    public function generatePluginLogic(string $pluginPrefix, string $pluginName): string
    {
        $constantDefinition = strtoupper($pluginName) . '_FILE';

        return "<?php\n\nnamespace {$pluginPrefix};\n\nuse Exception;\nuse {$pluginPrefix}\\Core\\Managers\\ServiceProviderManager;\n\nclass Plugin\n{\n    /**\n     * @throws Exception\n     */\n    public function __construct()\n    {\n        \$this->registerHooks();\n    }\n\n    private function registerHooks(): void {\n        register_activation_hook( {$constantDefinition}, [ \$this, 'activate' ] );\n        register_deactivation_hook( {$constantDefinition}, [ \$this, 'deactivate' ] );\n        register_uninstall_hook( {$constantDefinition}, [ __CLASS__, 'uninstall' ] );\n    }\n\n    public function activate(): void\n    {\n        ServiceProviderManager::getInstance()->register();\n        flush_rewrite_rules();\n    }\n\n    public function deactivate(): void\n    {\n        flush_rewrite_rules();\n    }\n\n    public static function uninstall(): void\n    {\n        // Uninstall logic here\n    }\n}";
    }
    public function generateProviderManagerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class ProviderManager\n{\n    protected array \$providers = [];\n\n    public function register(): void\n    {\n        foreach (\$this->providers as \$providerClass) {\n            \$provider = new \$providerClass();\n            \$provider->register();\n        }\n    }\n}";
    }

    public function generateServiceProviderLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nuse {$pluginPrefix}\\Core\\Support\\Container;\n\nabstract class ServiceProvider\n{\n    protected array \$services = [];\n\n    public function register(): void\n    {\n        foreach (\$this->services as \$service => \$dependencies)\n        {\n            if (is_string(\$service) && is_array(\$dependencies))\n            {\n                Container::bind(\$service, function () use (\$service, \$dependencies)\n                {\n                    \$resolvedDependencies = array_map(fn(\$dep) => Container::get(\$dep), \$dependencies);\n                    return new \$service(...\$resolvedDependencies);\n                });\n            } elseif (is_string(\$service))\n            {\n                Container::bind(\$service, function () use (\$service, \$dependencies)\n                {\n                    return new \$service(Container::get(\$dependencies));\n                });\n            } else {\n                Container::bind(\$dependencies, function () use (\$dependencies)\n                {\n                    return new \$dependencies();\n                });\n            }\n        }\n    }\n }";
    }

    public function generateDashboardControllerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class DashboardController\n{\n    protected string \$menuSlug;\n    protected string \$pageTitle;\n    protected string \$menuTitle;\n    protected string \$capability;\n    protected string \$view;\n\n    public function __construct() {\n        add_action('admin_menu', [\$this, 'addMenu']);\n    }\n\n    abstract public function addMenu(): void;\n    abstract public function processForm(): void;\n    abstract public function view(): void;\n\n    public function render(): void {\n        if (!current_user_can(\$this->capability)) {\n            wp_die(__('You do not have sufficient permissions to access this page.'));\n        }\n        if (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n            \$this->processForm();\n        }\n        \$this->view();\n    }\n\n    protected function handle(string \$action, callable \$callback): void {\n        if (isset(\$_POST[\$action])) {\n            \$callback();\n        }\n    }\n}";
    }

    public function generateMenuControllerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class MenuController extends DashboardController\n{\n    public function addMenu(): void {\n        add_menu_page(\$this->pageTitle, \$this->menuTitle, \$this->capability, \$this->menuSlug, [\$this, 'render'], 'dashicons-admin-generic', 20);\n    }\n}";
    }

    public function generateSubmenuControllerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class SubmenuController extends DashboardController\n{\n    protected string \$parentSlug;\n\n    public function addMenu(): void {\n        add_submenu_page(\$this->parentSlug, \$this->pageTitle, \$this->menuTitle, \$this->capability, \$this->menuSlug, [\$this, 'render']);\n    }\n}";
    }
    public function generateIsSingletonTraitLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support\\Traits;\n\ntrait IsSingleton\n{\n    private static ?self \$instance = null;\n\n    private function __construct() {}\n\n    public static function getInstance(): self\n    {\n        if (static::\$instance === null) {\n            static::\$instance = new static();\n        }\n\n        return static::\$instance;\n    }\n}";
    }

    public function generateContainerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support;\n\nuse Exception;\n\nclass Container\n{\n    private static array \$instances = [];\n\n    public static function bind(string \$key, callable \$resolver): void\n    {\n        self::\$instances[\$key] = \$resolver;\n    }\n\n    /**\n     * @throws Exception\n     */\n    public static function get(string \$key)\n    {\n        if (isset(self::\$instances[\$key])) {\n            return call_user_func(self::\$instances[\$key]);\n        }\n        throw new Exception(\"Service not found: {\$key}\");\n    }\n}";
    }

    public function generateServiceProviderManagerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Managers;\n\nuse {$pluginPrefix}\\Core\\Foundation\\ProviderManager;\nuse {$pluginPrefix}\\Core\\Support\\Traits\\IsSingleton;\n\nclass ServiceProviderManager extends ProviderManager\n{\n    use IsSingleton;\n\n    protected array \$providers = [];\n}";
    }

    public function generateMigrationLogic(string $pluginPrefix): string
    {
        $pluginPrefixLowered = strtolower($pluginPrefix);
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nuse {$pluginPrefix}\\Core\\Support\\Traits\\DB;\n\nabstract class Migration\n{\n    use DB;\n    protected string \$charsetCollate;\n    protected string \$prefix = '{$pluginPrefixLowered}_';\n\n    public function __construct()\n    {\n        \$this->charsetCollate = \$this->db()->get_charset_collate();\n    }\n\n    abstract public function getTableName(): string;\n    abstract public function getSchema(): string;\n\n    public function getFullTableName(): string\n    {\n        return \$this->db()->prefix . \$this->prefix . \$this->getTableName();\n    }\n\n    public function up(): void\n    {\n        \$sql = \$this->getSchema();\n        if (!function_exists('dbDelta')) {\n            require_once ABSPATH . 'wp-admin/includes/upgrade.php';\n        }\n        dbDelta(\$sql);\n    }\n\n    public function down(): void\n    {\n        \$table = \$this->getFullTableName();\n        \$this->db()->query(\"DROP TABLE IF EXISTS {\$table}\");\n    }\n}";
    }

    public function generateDBLogic(string $pluginPrefix) : string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support\\Traits;\n\nuse wpdb;\n\ntrait DB\n{\n    /**\n     * Get the global wpdb instance.\n     *\n     * @return wpdb\n     */\n    protected function db(): wpdb\n    {\n        global \$wpdb;\n        return \$wpdb;\n    }\n}";
    }

    public function generateRouterLogic(string $pluginPrefix): string
    {
        $apiNamespace = strtolower($pluginPrefix) . "/v1";

        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Routing;\n\nclass Router\n{\n    private static string \$namespace = '{$apiNamespace}';\n\n    /**
         * Register a REST route.
         *
         * @param string \$route The route path.
         * @param array \$args The route arguments.
         */\n    public static function add(string \$route, array \$args = []): void\n    {\n        register_rest_route(self::\$namespace, \$route, \$args);\n    }\n\n    /**
         * Register multiple routes at once.
         *
         * @param string \$name The group name.
         * @param array \$routes An associative array of routes.
         */\n    public static function group(string \$name, array \$routes): void\n    {\n        foreach (\$routes as \$route => \$args) {\n            self::add(\$name . \$route, \$args);\n        }\n    }\n\n    /**
         * Set the namespace for the REST routes.
         *
         * @param string \$namespace The namespace to use.
         */\n    public static function setNamespace(string \$namespace): void\n    {\n        self::\$namespace = \$namespace;\n    }\n}";
    }

    public function generateComposerJson(string $pluginName, string $pluginPrefix): array
    {
        return [
            'name' => strtolower($pluginPrefix . '/' . $pluginName),
            'description' => 'A new WordPress plugin',
            'type' => 'project',
            'license' => 'MIT',
            'require' => [
                'php' => '>=8.1',
                "woocommerce/action-scheduler" => "^3.9",
            ],
            'autoload' => [
                'psr-4' => [
                    $pluginPrefix . '\\' => 'app/',
                ],
            ],
            'minimum-stability' => 'stable',
            'prefer-stable' => true,
        ];
    }
}

