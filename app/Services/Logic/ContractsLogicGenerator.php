<?php

namespace NikoGin\Services\Logic;

class ContractsLogicGenerator
{
    public function generateCronInterface(string $pluginPrefix) : string
    {
        return "<?php \n\ namespace {$pluginPrefix}\\Core\\Contracts;\n\ninterface CronInterface\n\n{\n\npublic const CRON_HOOK = '';\n\npublic static function schedule(): void;\n\npublic function handle(): void;\n\n}";
    }
}