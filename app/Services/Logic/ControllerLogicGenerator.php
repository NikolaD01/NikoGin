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
        $content = "<?php\n\nnamespace {$pluginPrefix}\\Http\\Controllers\\API;\n\nclass {$name} {\n    // Your REST logic here\n}\n";
        file_put_contents($dir . "/{$name}.php", $content);
    }

    private function generateMenuLogic(string $name, string $dir, string $pluginPrefix): void
    {
        $content = "<?php\n\nnamespace {$pluginPrefix}\\Http\\Controllers\\Dashboard\\Menu;\n\nclass {$name} {\n    // Your Menu logic here\n}\n";
        file_put_contents($dir . "/{$name}.php", $content);
    }

    private function generateSubMenuLogic(string $name, string $dir, string $pluginPrefix): void
    {

        $content = "<?php\n\nnamespace {$pluginPrefix}\\Http\\Controllers\\Dashboard\\SubMenu;\n\nclass {$name} {\n    // Your SubMenu logic here\n}\n";
        file_put_contents($dir . "/{$name}.php", $content);
    }
}