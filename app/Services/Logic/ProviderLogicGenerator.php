<?php

namespace NikoGin\Services\Logic;

class ProviderLogicGenerator
{
    public function generate(string $name,string $pluginPrefix, string $dir ): void
    {
        $content = $this->logic($name, $pluginPrefix);
        file_put_contents($dir . "/{$name}.php", $content );

        $this->updateServiceProviderManager($name, $pluginPrefix, $dir);
    }

    private function logic(string $name, string $pluginPrefix): string
    {

        return "<?php\n\nnamespace {$pluginPrefix}\\Http\\Providers;\n\nuse {$pluginPrefix}\\Core\\Foundation\\ServiceProvider;\n\nclass {$name} extends ServiceProvider\n{\n  public array \$services = []; \n}";
    }

    private function updateServiceProviderManager(string $name, string $pluginPrefix, string $dir): void
    {

        $managerPath = dirname($dir, 2) . "/Core/Managers/ServiceProviderManager.php";

        if (!file_exists($managerPath)) {
            return;
        }
        $className = "{$pluginPrefix}\\Http\\Providers\\{$name}";
        $fileContent = file_get_contents($managerPath);

        // Check if provider is already registered
        if (str_contains($fileContent, $className)) {
            return;
        }

        $importStatement = "use {$className};\n";
        if (!str_contains($fileContent, $importStatement)) {
            $fileContent = preg_replace('/<\?php\n\nnamespace [^;]+;/', "<?php\n\nnamespace sf\\Core\\Managers;\n\n$importStatement", $fileContent, 1);
        }
        // Find the `$providers` array and insert the new provider
        $updatedContent = preg_replace(
            '/protected array \$providers = \[([^\]]*)\];/s',
            "protected array \$providers = [\n        {$name}::class,\$1\n    ];",
            $fileContent
        );

        file_put_contents($managerPath, $updatedContent);
    }
}