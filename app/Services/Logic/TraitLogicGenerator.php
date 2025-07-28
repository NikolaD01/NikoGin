<?php

namespace NikoGin\Services\Logic;

class TraitLogicGenerator
{
    public static function generateIsSingletonTraitLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support\\Traits;\n\ntrait IsSingleton\n{\n    private static ?self \$instance = null;\n\n    private function __construct() {}\n\n    public static function getInstance(): self\n    {\n        if (static::\$instance === null) {\n            static::\$instance = new static();\n        }\n\n        return static::\$instance;\n    }\n}";
    }

    public static function generateDBLogic(string $pluginPrefix) : string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support\\Traits;\n\nuse wpdb;\n\ntrait DB\n{\n    /**\n     * Get the global wpdb instance.\n     *\n     * @return wpdb\n     */\n    protected function db(): wpdb\n    {\n        global \$wpdb;\n        return \$wpdb;\n    }\n}";
    }

    public static function generateHasPermissionsTrait(string $pluginPrefix): string
    {
        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Support\\Traits;

trait HasPermissions
{
    /**
     * Determine if the current user can store a resource.
     *
     * @return bool
     */
    public function storePermission(): bool
    {
        return current_user_can('edit_posts');
    }

    /**
     * Determine if the current user can update a resource.
     *
     * @return bool
     */
    public function updatePermission(): bool
    {
        return current_user_can('edit_posts');
    }

    /**
     * Determine if the current user can delete a resource.
     *
     * @return bool
     */
    public function destroyPermission(): bool
    {
        return current_user_can('delete_posts');
    }
}
PHP;
    }

}