<?php

namespace NikoGin\Services\Logic;

class ListenerLogicGenerator
{
    public function generate(array $data, string $pluginPrefix) : void
    {
        $content = $this->logic($data, $pluginPrefix);
        file_put_contents($data['dir'] . "/" . $data['name'] . ".php", $content);
    }

    private function logic(array $data, string $pluginPrefix) : string
    {
        $name = $data['name'];
        $action = $data['action'] ?? 'custom_action';
        $priority = $data['priority'] ?? 10;
        $argsCount = $data['args'] ?? 1;

        return "<?php\n\nnamespace {$pluginPrefix}\\Http\\Listeners;\n\nuse {$pluginPrefix}\\Core\\Attributes\\AsListener;\nuse {$pluginPrefix}\\Core\\Foundation\\Listener;\n\n#[AsListener('{$action}', priority: {$priority}, argsCount: {$argsCount})]\nclass {$name} extends Listener\n{\n    public function handle(mixed ...\$args): void\n    {\n        // Handle the {$action} action\n    }\n}";
    }

}