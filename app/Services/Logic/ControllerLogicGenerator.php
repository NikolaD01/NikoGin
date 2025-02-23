<?php

namespace NikoGin\Services\Logic;

class ControllerLogicGenerator
{
    public function generate(string $name, string $type, string $dir, string $pluginPrefix): void
    {
        match ($type) {
            'rest' => $this->generateRestLogic($name, $dir, $pluginPrefix),
            'menu' => $this->generateMenuLogic($name, $dir, $pluginPrefix),
            'submenu' => $this->generateSubMenuLogic($name, $dir, $pluginPrefix),
            default => throw new \InvalidArgumentException('Invalid controller type provided. Allowed types: rest, menu, submenu.')
        };
    }

    private function generateRestLogic(string $name, string $dir, string $pluginPrefix): void
    {
        $content = "<?php\n\nnamespace {$pluginPrefix}\\Http\\Controllers\\API;\n\nuse WP_REST_Request;\nuse WP_REST_Response;\n\nclass {$name} {\n    public function store(WP_REST_Request \$request): WP_REST_Response {\n        \$data = \$request->get_json_params();\n        return new WP_REST_Response();\n    }\n\n    public function index(): WP_REST_Response {\n        return new WP_REST_Response([\n            'success' => true,\n            'message' => '',\n        ]);\n    }\n}\n";
        file_put_contents($dir . "/{$name}.php", $content);
    }

    private function generateMenuLogic(string $name, string $dir, string $pluginPrefix): void
    {
        $menuSlug = strtolower(str_replace(' ', '_', $name));
        $content = "<?php\n\nnamespace {$pluginPrefix}\\Http\\Controllers\\Dashboard\\Menu;\n\nuse {$pluginPrefix}\\Core\\Foundation\\MenuController;\n\nclass {$name} extends MenuController\n{\n    protected string \$pageTitle = '{$name} Page';\n    protected string \$menuTitle = '{$name}';\n    protected string \$capability = 'manage_options';\n    protected string \$menuSlug = '{$menuSlug}';\n\n    public function processForm(): void {}\n    public function view(): void {}\n}\n";

        file_put_contents($dir . "/{$name}.php", $content);
    }

    private function generateSubMenuLogic(string $name, string $dir, string $pluginPrefix): void
    {
        $menuSlug = strtolower(str_replace(' ', '_', $name));
        $content = "<?php\n\nnamespace {$pluginPrefix}\\Http\\Controllers\\Dashboard\\SubMenu;\n\nuse {$pluginPrefix}\\Core\\Foundation\\SubmenuController;\n\nclass {$name} extends SubmenuController\n{\n    protected string \$parentSlug = '{$parentSlug}';\n    protected string \$pageTitle = '{$name} Page';\n    protected string \$menuTitle = '{$name}';\n    protected string \$capability = 'manage_options';\n    protected string \$menuSlug = '{$menuSlug}';\n\n    public function processForm(): void {}\n    public function view(): void {}\n}\n";

        file_put_contents($dir . "/{$name}.php", $content);
    }
}