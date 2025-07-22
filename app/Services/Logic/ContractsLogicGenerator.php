<?php

namespace NikoGin\Services\Logic;

class ContractsLogicGenerator
{
    public static function generateCronInterface(string $pluginPrefix) : string
    {
        return "<?php \n\n namespace {$pluginPrefix}\\Core\\Contracts;\n\ninterface CronInterface\n\n{\n\npublic const CRON_HOOK = '';\n\npublic static function schedule(): void;\n\npublic function handle(): void;\n\n}";
    }

    public static function generateBootable(string $pluginPrefix) : string
    {
        return "<?php \n\n namespace {$pluginPrefix}\\Core\\Contracts;\n\ninterface Bootable\n\n{\n\npublic static function boot(): void;\n\n}";
    }

    public static function generateMiddlewareInterface(string $pluginPrefix) : string
    {
        return "<?php \n\n namespace {$pluginPrefix}\\Core\\Contracts;\n\nuse WP_Error;\nuse WP_REST_Request;\n\ninterface MiddlewareInterface\n\n{\n\npublic static function verify(WP_REST_Request \$request): bool|WP_Error;\n\n}";
    }

}