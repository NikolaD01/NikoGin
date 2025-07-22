<?php

namespace NikoGin\Services\Logic;

class BaseLogicGenerator
{

    public static function generateMainFileLogic(string $pluginPrefix, string $pluginName): string
    {
        // Generate the constant names
        //Note: Also could be replaced with symfony/string package method
        $constantName = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName)));
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
 */\n\n// Define constants\nif ( ! defined( '{$constantFile}' ) ) {\n    define( '{$constantFile}', __FILE__ );\n}\n\nif ( ! defined( '{$constantDir}' ) ) {\n    define( '{$constantDir}', plugin_dir_path( __FILE__ ) );\n}\n\nif ( ! defined( '{$constantUrl}' ) ) {\n    define( '{$constantUrl}', plugin_dir_url( __FILE__ ) );\n}\n\n// Load the Composer autoloader\nrequire_once __DIR__ . '/vendor/autoload.php';\n\n// Instantiate the Plugin class\nuse {$pluginPrefix}\\Bootstrap;\nBootstrap::init();";
    }
    public static function generateBootstrapLogic(string $pluginPrefix, string $pluginName): string
    {
        $constantDefinition = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName))) . '_FILE';

        return "<?php

namespace {$pluginPrefix};

use {$pluginPrefix}\\Core\\Bootstrap\\Activator;
use {$pluginPrefix}\\Core\\Bootstrap\\Deactivator;
use {$pluginPrefix}\\Core\\Bootstrap\\Loader;
use {$pluginPrefix}\\Core\\Bootstrap\\Uninstaller;
use {$pluginPrefix}\\Core\\Bootstrap\\RoutesRegistrar;
class Bootstrap
{
    /** @var class-string[] */
    private static array \$bootstraps = [
        Activator::class,
        Deactivator::class,
        Loader::class,
        Uninstaller::class,
        RoutesRegistrar::class,
    ];

    /**
     * Kick off all bootstrap components.
     */
    public static function init(): void
    {
        if (! defined(self::PLUGIN_FILE)) {
            define(self::PLUGIN_FILE, __FILE__);
        }

        foreach (self::\$bootstraps as \$bootClass) {
            \$bootClass::boot();
        }
    }

    private const PLUGIN_FILE = '{$constantDefinition}';
}
";
    }

    public static function generateProviderManagerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class ProviderManager\n{\n    protected array \$providers = [];\n\n    public function register(): void\n    {\n        foreach (\$this->providers as \$providerClass) {\n            \$provider = new \$providerClass();\n            \$provider->register();\n        }\n    }\n}";
    }

    public static function generateServiceProviderLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nuse {$pluginPrefix}\\Core\\Support\\Container;\n\nabstract class ServiceProvider\n{\n    protected array \$services = [];\n\n    public function register(): void\n    {\n        foreach (\$this->services as \$service => \$dependencies)\n        {\n            if (is_string(\$service) && is_array(\$dependencies))\n            {\n                Container::bind(\$service, function () use (\$service, \$dependencies)\n                {\n                    \$resolvedDependencies = array_map(fn(\$dep) => Container::get(\$dep), \$dependencies);\n                    return new \$service(...\$resolvedDependencies);\n                });\n            } elseif (is_string(\$service))\n            {\n                Container::bind(\$service, function () use (\$service, \$dependencies)\n                {\n                    return new \$service(Container::get(\$dependencies));\n                });\n            } else {\n                Container::bind(\$dependencies, function () use (\$dependencies)\n                {\n                    return new \$dependencies();\n                });\n            }\n        }\n    }\n }";
    }

    public static function generateDashboardControllerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class DashboardController\n{\n    protected string \$menuSlug;\n    protected string \$pageTitle;\n    protected string \$menuTitle;\n    protected string \$capability;\n    protected string \$view;\n\n    public function __construct() {\n        add_action('admin_menu', [\$this, 'addMenu']);\n    }\n\n    abstract public function addMenu(): void;\n    abstract public function processForm(): void;\n    abstract public function view(): void;\n\n    public function render(): void {\n        if (!current_user_can(\$this->capability)) {\n            wp_die(__('You do not have sufficient permissions to access this page.'));\n        }\n        if (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n            \$this->processForm();\n        }\n        \$this->view();\n    }\n\n    protected function handle(string \$action, callable \$callback): void {\n        if (isset(\$_POST[\$action])) {\n            \$callback();\n        }\n    }\n}";
    }

    public static function generateMenuControllerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class MenuController extends DashboardController\n{\n    public function addMenu(): void {\n        add_menu_page(\$this->pageTitle, \$this->menuTitle, \$this->capability, \$this->menuSlug, [\$this, 'render'], 'dashicons-admin-generic', 20);\n    }\n}";
    }

    public static function generateSubmenuControllerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class SubmenuController extends DashboardController\n{\n    protected string \$parentSlug;\n\n    public function addMenu(): void {\n        add_submenu_page(\$this->parentSlug, \$this->pageTitle, \$this->menuTitle, \$this->capability, \$this->menuSlug, [\$this, 'render']);\n    }\n}";
    }
    public static function generateIsSingletonTraitLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support\\Traits;\n\ntrait IsSingleton\n{\n    private static ?self \$instance = null;\n\n    private function __construct() {}\n\n    public static function getInstance(): self\n    {\n        if (static::\$instance === null) {\n            static::\$instance = new static();\n        }\n\n        return static::\$instance;\n    }\n}";
    }

    public static function generateContainerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support;\n\nuse Exception;\n\nclass Container\n{\n    private static array \$instances = [];\n\n    public static function bind(string \$key, callable \$resolver): void\n    {\n        self::\$instances[\$key] = \$resolver;\n    }\n\n    /**\n     * @throws Exception\n     */\n    public static function get(string \$key)\n    {\n        if (isset(self::\$instances[\$key])) {\n            return call_user_func(self::\$instances[\$key]);\n        }\n        throw new Exception(\"Service not found: {\$key}\");\n    }\n}";
    }

    public static function generateServiceProviderManagerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Managers;\n\nuse {$pluginPrefix}\\Core\\Foundation\\ProviderManager;\nuse {$pluginPrefix}\\Core\\Support\\Traits\\IsSingleton;\n\nclass ServiceProviderManager extends ProviderManager\n{\n    use IsSingleton;\n\n    protected array \$providers = [];\n}";
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

    public static function generateListenerManagerLogic(string $pluginPrefix): string
    {
        return "<?php

        namespace {$pluginPrefix}\\Core\\Managers;
        
        use ReflectionClass;
        use {$pluginPrefix}\\Core\Foundation\\Listener;
        use {$pluginPrefix}\\Core\\Attributes\\AsListener;
        
        class ListenerManager
        {
            protected array \$listeners = [];
        
            public function registerListener(string \$listenerClass): void
            {
                if (!is_subclass_of(\$listenerClass, Listener::class)) {
                    return;
                }
        
                \$this->listeners[] = \$listenerClass;
            }
        
            public function register(): void
            {
                foreach (\$this->listeners as \$listenerClass) {
                    \$reflection = new ReflectionClass(\$listenerClass);
                    \$attributes = \$reflection->getAttributes(AsListener::class);
        
                    if (empty(\$attributes)) {
                        continue;
                    }
        
                    /** @var AsListener \$config */
                    \$config = \$attributes[0]->newInstance();
        
                    \$listenerInstance = new \$listenerClass();
        
                    if (\$config->type === 'hook') {
                        if (!has_action(\$config->name, [\$listenerInstance, 'handle'])) {
                            add_action(\$config->name, [\$listenerInstance, 'handle'], \$config->priority, \$config->argsCount);
                        }
                    } elseif (\$config->type === 'filter') {
                        if (!has_filter(\$config->name, [\$listenerInstance, 'handle'])) {
                            add_filter(\$config->name, [\$listenerInstance, 'handle'], \$config->priority, \$config->argsCount);
                        }
                    }
                }
            }
        }";
    }

    // Migration
    public static function generateMigrationLogic(string $pluginPrefix): string
    {
        $pluginPrefixLowered = strtolower($pluginPrefix);
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nuse {$pluginPrefix}\\Core\\Support\\Traits\\DB;\n\nabstract class Migration\n{\n    use DB;\n    protected string \$charsetCollate;\n    protected string \$prefix = '{$pluginPrefixLowered}_';\n\n    public function __construct()\n    {\n        \$this->charsetCollate = \$this->db()->get_charset_collate();\n    }\n\n    abstract public function getTableName(): string;\n    abstract public function getSchema(): string;\n\n    public function getFullTableName(): string\n    {\n        return \$this->db()->prefix . \$this->prefix . \$this->getTableName();\n    }\n\n    public function up(): void\n    {\n        \$sql = \$this->getSchema();\n        if (!function_exists('dbDelta')) {\n            require_once ABSPATH . 'wp-admin/includes/upgrade.php';\n        }\n        dbDelta(\$sql);\n    }\n\n    public function down(): void\n    {\n        \$table = \$this->getFullTableName();\n        \$this->db()->query(\"DROP TABLE IF EXISTS {\$table}\");\n    }\n}";
    }

   // Shortcode Component
    public static function generateShortcode(string $pluginPrefix): string
    {
        return "<?php

namespace {$pluginPrefix}\\Core\\Foundation;

use Exception;

abstract class Shortcode
{
    protected string \$tag;

    /**
     * @throws Exception
     */
    public function __construct(string \$tag)
    {
        \$this->tag = \$tag;
        if (empty(\$this->tag)) {
            throw new Exception('Shortcode tag is not defined in ' . static::class);
        }

        add_shortcode(\$this->tag, [\$this, 'handle']);
    }

    /**
     * Must be implemented by all subclasses.
     *
     * @param array<string, mixed> \$attrs
     * @param string|null          \$content
     * @return string
     */
    abstract public function handle(array \$attrs = [], string \$content = null): string;
}";
    }
    public static function generateListenerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Foundation;\n\nabstract class Listener\n{\n\n    abstract public function handle(mixed ...\$args): void;\n}";
    }

    public static function generateAsListenerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Attributes;\n\nuse Attribute;\n\n#[Attribute(Attribute::TARGET_CLASS)]\nclass AsListener\n{\n    public function __construct(\n        public string \$name,\n    public string \$type,\n   public int \$priority = 10,\n        public int \$argsCount = 1\n    ) {}\n}";
    }
    public static function generateRouterLogic(string $pluginPrefix): string
    {
        $apiNamespace = strtolower($pluginPrefix) . '/v1';

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Support;

use {$pluginPrefix}\\Core\\Contracts\\MiddlewareInterface;
use {$pluginPrefix}\\Core\\Support\\Container;
use InvalidArgumentException;
use WP_REST_Server;

class Router
{
    /** @var string REST namespace (e.g. 'myplugin/v1') */
    private static string \$namespace = '{$apiNamespace}';

    /**
     * Register a REST route.
     */
    public static function add(string \$route, array \$args = []): void
    {
        register_rest_route(self::\$namespace, \$route, \$args);
    }

    /**
     * Register multiple routes under a common prefix.
     */
    public static function group(string \$prefix, callable \$routesRegistrar): void
    {
        \$orig = self::\$namespace;
        self::\$namespace = rtrim(self::\$namespace, '/') . \$prefix;
        \$routesRegistrar();
        self::\$namespace = \$orig;
    }

    /**
     * Register a route with a middleware/permission callback.
     */
    public static function middleware(string \$middlewareClass, string \$route, array \$args = []): void
    {
        \$middleware = Container::get(\$middlewareClass);
        if (! \$middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException("\$middlewareClass must implement MiddlewareInterface");
        }
        \$args['permission_callback'] = [ \$middleware, 'verify' ];
        self::add(\$route, \$args);
    }

    /**
     * Register a full set of RESTful routes for a resource.
     *
     * @param string \$baseRoute       e.g. '/items'
     * @param string \$controllerClass Fully‑qualified controller class with methods:
     *                                  index, show, store, update, destroy
     */
    public static function resource(string \$baseRoute, string \$controllerClass): void
    {
        // index (READABLE)
        self::add(\$baseRoute, [
            'methods'  => WP_REST_Server::READABLE,
            'callback' => [\$controllerClass, 'index'],
        ]);

        // show (READABLE)
        self::add(\$baseRoute . '/(?P<id>\\d+)', [
            'methods'  => WP_REST_Server::READABLE,
            'callback' => [\$controllerClass, 'show'],
            'args'     => ['id' => ['validate_callback' => 'is_numeric']],
        ]);

        // store (CREATABLE)
        self::add(\$baseRoute, [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [\$controllerClass, 'store'],
            'permission_callback' => [Container::get(\$controllerClass), 'storePermission'] ?? '__return_true',
        ]);

        // update (EDITABLE)
        self::add(\$baseRoute . '/(?P<id>\\d+)', [
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => [\$controllerClass, 'update'],
            'permission_callback' => [Container::get(\$controllerClass), 'updatePermission'] ?? '__return_true',
            'args'                => ['id' => ['validate_callback' => 'is_numeric']],
        ]);

        // destroy (DELETABLE)
        self::add(\$baseRoute . '/(?P<id>\\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [\$controllerClass, 'destroy'],
            'permission_callback' => [Container::get(\$controllerClass), 'destroyPermission'] ?? '__return_true',
            'args'                => ['id' => ['validate_callback' => 'is_numeric']],
        ]);
    }

    /**
     * Change the REST namespace (e.g. 'myplugin/v2').
     */
    public static function setNamespace(string \$namespace): void
    {
        self::\$namespace = \$namespace;
    }
}
PHP;
    }

    public static function generateComposerJson(string $directoryName, string $pluginPrefix): array
    {
        return [
            'name' => strtolower($pluginPrefix . '/' . $directoryName),
            'description' => 'A new WordPress plugin',
            'type' => 'project',
            'license' => 'MIT',
            'require' => [
                'php' => '>=8.1',
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

    // Repository + DB trait
    public static function generateDBLogic(string $pluginPrefix) : string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support\\Traits;\n\nuse wpdb;\n\ntrait DB\n{\n    /**\n     * Get the global wpdb instance.\n     *\n     * @return wpdb\n     */\n    protected function db(): wpdb\n    {\n        global \$wpdb;\n        return \$wpdb;\n    }\n}";
    }
    public static function generateRepository(string $pluginPrefix): string
    {
        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Foundation;

use {$pluginPrefix}\\Core\\Support\\Traits\\DB;

abstract class Repository
{
    use DB;

    protected string \$table;

    public function __construct(string \$tableName)
    {
        \$this->table = \$this->table(\$tableName);
    }

    public function getTable(): string
    {
        return \$this->table;
    }

    /**
     * Get the table name with prefix.
     */
    protected function table(string \$tableName): string
    {
        return \$this->db()->prefix . \$tableName;
    }

    /**
     * Insert a record into the table.
     */
    public function insert(array \$data): int
    {
        \$this->db()->insert(\$this->table, \$data);
        return \$this->db()->insert_id;
    }

    /**
     * Update a record in the table.
     */
    public function update(array \$data, array \$where): bool|int
    {
        return \$this->db()->update(\$this->table, \$data, \$where);
    }

    /**
     * Delete a record from the table.
     */
    public function delete(string \$condition, array \$values = []): bool|int
    {
        \$sql = "DELETE FROM {\$this->table} WHERE {\$condition}";
        \$prepared = \$this->db()->prepare(\$sql, \$values);
        return \$this->db()->query(\$prepared);
    }

    /**
     * Get all records from the table, optionally ordered.
     */
    public function getAll(string \$orderBy = null): array
    {
        \$sql = "SELECT * FROM {\$this->table}";
        if (\$orderBy) {
            \$sql .= " ORDER BY {\$orderBy}";
        }
        return \$this->db()->get_results(\$sql);
    }

    /**
     * Get records with WHERE and custom SELECT columns.
     */
    public function getAllWhere(array \$where = null, array \$select = null): ?array
    {
        \$columns = '*';
        if (\$select) {
            \$columns = implode(', ', array_map(fn(\$col) => "`{\$col}`", \$select));
        }

        \$conditions = [];
        \$values = [];
        if (\$where) {
            foreach (\$where as \$key => \$value) {
                if (stripos(\$key, 'LIKE') !== false) {
                    \$col = str_replace(' LIKE', '', \$key);
                    \$conditions[] = "`{\$col}` LIKE %s";
                    \$values[]     = \$value;
                } else {
                    \$conditions[] = "`{\$key}` = %s";
                    \$values[]     = \$value;
                }
            }
        }

        \$sql = "SELECT {\$columns} FROM {\$this->table}";
        if (\$conditions) {
            \$sql .= " WHERE " . implode(" AND ", \$conditions);
        }

        \$prepared = \$this->db()->prepare(\$sql, \$values);
        return \$this->db()->get_results(\$prepared);
    }

    /**
     * Get a single record by conditions.
     */
    public function getOne(array \$where): ?object
    {
        \$parts = [];
        foreach (\$where as \$col => \$val) {
            \$parts[] = \$this->db()->prepare("`{\$col}` = %s", \$val);
        }
        \$sql = "SELECT * FROM {\$this->table} WHERE " . implode(' AND ', \$parts) . " LIMIT 1";
        return \$this->db()->get_row(\$sql);
    }
}
PHP;
    }

    public static function generateHTTPLogic(string $pluginPrefix): string
    {
        return <<<PHP
<?php
namespace {$pluginPrefix}\\Core\\Support;

class HTTP
{
    /**
     * Send a GET request
     */
    public static function get(string \$url, array \$headers = []): array
    {
        return self::request('GET', \$url, [], \$headers);
    }

    /**
     * Send a POST request
     */
    public static function post(string \$url, array \$data = [], array \$headers = []): array
    {
        return self::request('POST', \$url, \$data, \$headers);
    }

    /**
     * Send a PUT request
     */
    public static function put(string \$url, array \$data = [], array \$headers = []): array
    {
        return self::request('PUT', \$url, \$data, \$headers);
    }

    /**
     * Send a DELETE request
     */
    public static function delete(string \$url, array \$headers = []): array
    {
        return self::request('DELETE', \$url, [], \$headers);
    }

    /**
     * Generic request handler
     */
    protected static function request(string \$method, string \$url, array \$data = [], array \$headers = []): array
    {
        \$args = [
            'method'  => strtoupper(\$method),
            'headers' => array_merge([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ], \$headers),
        ];

        if (!empty(\$data)) {
            \$args['body'] = json_encode(\$data);
        }

        \$response = wp_remote_request(\$url, \$args);

        if (is_wp_error(\$response)) {
            return [
                'success' => false,
                'error'   => \$response->get_error_message(),
            ];
        }

        \$code = wp_remote_retrieve_response_code(\$response);
        \$body = wp_remote_retrieve_body(\$response);

        return [
            'success' => \$code >= 200 && \$code < 300,
            'code'    => \$code,
            'body'    => json_decode(\$body, true),
            'raw'     => \$body,
        ];
    }
}
PHP;

    }


}

