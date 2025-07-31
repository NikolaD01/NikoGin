<?php

namespace NikoGin\Services\Logic;

class AttributesLogicGenerator
{
    public static function generateAsListenerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Attributes;\n\nuse Attribute;\n\n#[Attribute(Attribute::TARGET_CLASS)]\nclass AsListener\n{\n    public function __construct(\n        public string \$name,\n    public string \$type,\n   public int \$priority = 10,\n        public int \$argsCount = 1\n    ) {}\n}";
    }
}