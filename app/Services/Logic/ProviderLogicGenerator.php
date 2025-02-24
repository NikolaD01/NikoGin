<?php

namespace NikoGin\Services\Logic;

class ProviderLogicGenerator
{
    public function generate(string $name,string $pluginPrefix, string $dir ): void
    {
        $content = $this->logic($name, $pluginPrefix);
        file_put_contents($dir . "/{$name}.php", $content );
    }

    private function logic(string $name, string $pluginPrefix): string
    {

        return "<?php\n\nnamespace {$pluginPrefix}\\Http\\Providers;\n\nuse {$pluginPrefix}\\Core\\Foundation\\ServiceProvider;\n\nclass {$name} extends ServiceProvider\n{\n  public array \$services = []; \n}";
    }
}