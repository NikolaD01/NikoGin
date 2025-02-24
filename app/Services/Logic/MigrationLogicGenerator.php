<?php

namespace NikoGin\Services\Logic;

class MigrationLogicGenerator
{

    public function generate(string $name,string $pluginPrefix, string $dir ): void
    {
        $content = $this->logic($name, $pluginPrefix, $dir);
        file_put_contents($dir . "/{$name}.php", $content );
    }

    private function logic(string $name, string $pluginPrefix): string
    {
        $lowName = strtolower($name);
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Database\\Migrations;\n\nuse {$pluginPrefix}\\Core\\Foundation\\Migration;\n\nclass {$name} extends Migration\n{\n    public function getTableName(): string\n    {\n        return '{$lowName}';\n    }\n\n    public function getSchema(): string\n    {\n        \$table = \$this->getFullTableName();\n        return \"CREATE TABLE IF NOT EXISTS {\$table} () {\$this->charsetCollate};\";\n    }\n}";
    }


}