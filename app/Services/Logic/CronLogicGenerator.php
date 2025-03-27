<?php

namespace NikoGin\Services\Logic;

class CronLogicGenerator
{
    public function generate(string $name, string $pluginPrefix, string $cronDir): void
    {
        $filePath = "$cronDir/$name.php";

        // Ensure the directory exists
        if (!file_exists($cronDir)) {
            mkdir($cronDir, 0777, true);
        }

        if (!file_exists($filePath)) {
            $content = $this->generateCronClass($name, $pluginPrefix);
            file_put_contents($filePath, $content);
        }
    }

    public function generateProvider(string $name, string $pluginPrefix, string $providerDir): void
    {
        $filePath = "$providerDir/CronProvider.php";
        // Ensure the directory exists
        if (!file_exists($providerDir)) {
            mkdir($providerDir, 0777, true);
        }

        if (!file_exists($filePath)) {
            $content = $this->generateCronProviderClass($name, $pluginPrefix);
        } else {
            $cronNamespace = "{$pluginPrefix}\\Http\\Crons\\$name";
            $content = file_get_contents($filePath);

            // Check if the cron job is already registered
            if (!str_contains($content, "{$cronNamespace}::class")) {
                // Append the new cron inside the $crons array
                $content = preg_replace_callback(
                    '/protected\s+array\s+\$crons\s*=\s*\[(.*?)\n\s*\];/s',
                    function ($matches) use ($cronNamespace) {
                        $existingCrons = trim($matches[1]);

                        // Normalize existing entries (remove trailing commas)
                        $existingCrons = rtrim($existingCrons, ',');

                        // If the array is empty, add the first cron correctly
                        if (empty($existingCrons)) {
                            return "protected array \$crons = [\n        {$cronNamespace}::class => [],\n    ];";
                        }

                        // Append to existing crons, ensuring no double commas
                        return "protected array \$crons = [\n        {$existingCrons},\n        {$cronNamespace}::class => []\n    ];";
                    },
                    $content
                );
            }
        }

        file_put_contents($filePath, $content);
    }

    private function generateCronClass(string $name, string $pluginPrefix): string
    {

        $lowerPluginPrefix = strtolower($pluginPrefix);

        return "<?php

namespace {$pluginPrefix}\\Http\\Crons;

use {$pluginPrefix}\\Core\\Contracts\\CronInterface;

class {$name} implements CronInterface
{
    public const CRON_HOOK = '{$lowerPluginPrefix}_". strtolower($name) ."';

    public function __construct()
    {}

    public static function schedule(): void
    {
        \$timestamp = strtotime('tomorrow midnight');

        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(\$timestamp, 'daily', self::CRON_HOOK);
        }
    }

    public function handle(): void
    {
        // Cron job logic here

        self::schedule();
    }
}";
    }

    private function generateCronProviderClass(string $name, string $pluginPrefix): string
    {
        return "<?php

namespace {$pluginPrefix}\\Http\\Providers;

use {$pluginPrefix}\\Core\\Foundation\\ServiceProvider;
use {$pluginPrefix}\\Core\\Contracts\\CronInterface;
use {$pluginPrefix}\\Core\\Support\\Container;

class CronProvider extends ServiceProvider
{
    protected array \$crons = [
    ];

    public function register(): void
    {
        foreach (\$this->crons as \$cron => \$dependencies) {
            if (is_subclass_of(\$cron, CronInterface::class)) {
                Container::bind(\$cron, function () use (\$cron, \$dependencies) {
                    \$resolvedDependencies = array_map(fn(\$dependency) => Container::get(\$dependency), \$dependencies);
                    return new \$cron(...\$resolvedDependencies);
                });

                \$cron::schedule();
                add_action(\$cron::CRON_HOOK, [Container::get(\$cron), 'handle']);
            }
        }
    }
}";
    }
}