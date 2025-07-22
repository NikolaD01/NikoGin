<?php

namespace NikoGin\Services\Logic;

class ListenerLogicGenerator
{
    public static function generate(array $data, string $pluginPrefix) : void
    {
        $content = self::logic($data, $pluginPrefix);
        file_put_contents($data['dir'] . "/" . $data['name'] . ".php", $content);
        self::registerListener($data['name'], $pluginPrefix, $data['dir']);
    }

    private static function logic(array $data, string $pluginPrefix) : string
    {
        $name = $data['name'];
        $action = $data['listener'] ?? 'custom_action';
        $type = $data['type'] ?? "action";
        $priority = $data['priority'] ?? 10;
        $argsCount = $data['args'] ?? 1;

        return "<?php\n\nnamespace {$pluginPrefix}\\Http\\Listeners;\n\nuse {$pluginPrefix}\\Core\\Attributes\\AsListener;\nuse {$pluginPrefix}\\Core\\Foundation\\Listener;\n\n#[AsListener(name: '{$action}', type: '{$type}', priority: {$priority}, argsCount: {$argsCount})]\nclass {$name} extends Listener\n{\n    public function handle(mixed ...\$args): void\n    {\n        // Handle the {$action} action\n    }\n}";
    }

    private static function registerListener(string $name, string $pluginPrefix, string $dir): void
    {
        $managerFile = dirname($dir, 2) . "/Core/Managers/ListenerManager.php";

        if (!file_exists($managerFile)) {
            return;
        }



        $managerContent = file_get_contents($managerFile);
        $classReference = "\\{$pluginPrefix}\\Http\\Listeners\\{$name}";

        if(!str_contains($managerContent, $classReference)) {
            $managerContent = preg_replace(
                '/protected array\s+\$listeners\s*=\s*\[/',
                "protected array \$listeners = [\n        {$classReference}::class,",
                $managerContent,
                1
            );
            file_put_contents($managerFile, $managerContent);
        }
    }
}