<?php

namespace NikoGin\Services\Logic;

class BaseLogicGenerator
{


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
use {$pluginPrefix}\\Core\\Bootstrap\\BlockRegistrar;
use {$pluginPrefix}\\Core\\Bootstrap\AssetsRegistrar;
class Bootstrap
{
    /** @var class-string[] */
    private static array \$bootstraps = [
        Activator::class,
        Deactivator::class,
        Loader::class,
        Uninstaller::class,
        RoutesRegistrar::class,
        BlockRegistrar::class,
        AssetsRegistrar::class
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

    public static function generateJob(string $pluginPrefix): string
    {
        $lowerPrefix = strtolower($pluginPrefix);

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Foundation;

abstract class Job
{
    final public function __construct()
    {
        add_action("{$lowerPrefix}_" . \$this->getActionHook() . "_job", [\$this, 'handle'], 10, \$this->getNumOfArgs());
    }

    /**
     * Get the action hook name (without prefix/suffix).
     */
    abstract protected function getActionHook(): string;

    /**
     * Number of args passed to the job handler.
     */
    abstract protected function getNumOfArgs(): int;

    /**
     * Handle the job logic.
     */
    abstract public function handle(...\$args): void;
}
PHP;
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

    public static function generateComposerJson(string $directoryName, string $pluginPrefix): array
    {
        return [
            'name' => strtolower($pluginPrefix . '/' . $directoryName),
            'description' => 'A new WordPress plugin',
            'type' => 'project',
            'license' => 'MIT',
            'require' => [
                'php' => '>=8.2',
                "woocommerce/action-scheduler" => "^3.9"
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

    // Repository

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

}

