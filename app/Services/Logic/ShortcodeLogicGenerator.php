<?php

namespace NikoGin\Services\Logic;

class ShortcodeLogicGenerator
{

    public function generate(string $name, string $action, string $shortcodeDir, string $pluginPrefix): void
    {
        $className = $name . 'Shortcode';
        $filePath  = rtrim($shortcodeDir, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $className
            . '.php';

        // Ensure the directory exists
        if (! file_exists($shortcodeDir)) {
            mkdir($shortcodeDir, 0777, true);
        }

        // Only generate if file does not already exist
        if (! file_exists($filePath)) {
            $content = $this->generateShortcode($name, $action, $pluginPrefix);
            file_put_contents($filePath, $content);
        }
    }

    /**
     * Build the actual shortcode class code.
     */
    private function generateShortcode(string $name, string $action, string $pluginPrefix): string
    {
        $className    = $name . 'Shortcode';
        $namespace    = $pluginPrefix . '\\Http\\Admin\\Shortcodes';
        $baseShortcode = $pluginPrefix . '\\Core\\Foundation\\Shortcode';

        return <<<PHP
<?php

namespace {$namespace};

use {$baseShortcode};

class {$className} extends Shortcode
{
    public function __construct()
    {
        parent::__construct('{$action}');
    }

    public function handle(array \$attrs = [], string \$content = null): string
    {
        ob_start();

        // TODO: implement your shortcode logic here

        return ob_get_clean();
    }
}
PHP;
    }
}