<?php

namespace NikoGin\Services\Logic;

class MigrationLogicGenerator
{

    public static function generate(string $name,string $pluginPrefix, string $dir ): void
    {
        $content = self::logic($name, $pluginPrefix);
        file_put_contents($dir . "/{$name}.php", $content );
    }

    private static function logic(string $name, string $pluginPrefix): string
    {
        $lowName = strtolower($name);
        return "<?php\n\nnamespace {$pluginPrefix}\\Http\\Migrations;\n\nuse {$pluginPrefix}\\Core\\Foundation\\Migration;\n\nclass {$name} extends Migration\n{\n    public function getTableName(): string\n    {\n        return '{$lowName}';\n    }\n\n    public function getSchema(): string\n    {\n        \$table = \$this->getFullTableName();\n        return \"CREATE TABLE IF NOT EXISTS {\$table} () {\$this->charsetCollate};\";\n    }\n}";
    }


}