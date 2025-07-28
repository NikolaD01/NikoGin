<?php

namespace NikoGin\Services\Logic;

class ManagerLogicGenerator
{
    public static function generateListenerManagerLogic(string $pluginPrefix): string
    {
        return "<?php

        namespace {$pluginPrefix}\\Core\\Managers;
        
        use ReflectionClass;
        use {$pluginPrefix}\\Core\Foundation\\Listener;
        use {$pluginPrefix}\\Core\\Attributes\\AsListener;
        
        class ListenerManager
        {
            protected array \$listeners = [];
        
            public function registerListener(string \$listenerClass): void
            {
                if (!is_subclass_of(\$listenerClass, Listener::class)) {
                    return;
                }
        
                \$this->listeners[] = \$listenerClass;
            }
        
            public function register(): void
            {
                foreach (\$this->listeners as \$listenerClass) {
                    \$reflection = new ReflectionClass(\$listenerClass);
                    \$attributes = \$reflection->getAttributes(AsListener::class);
        
                    if (empty(\$attributes)) {
                        continue;
                    }
        
                    /** @var AsListener \$config */
                    \$config = \$attributes[0]->newInstance();
        
                    \$listenerInstance = new \$listenerClass();
        
                    if (\$config->type === 'hook') {
                        if (!has_action(\$config->name, [\$listenerInstance, 'handle'])) {
                            add_action(\$config->name, [\$listenerInstance, 'handle'], \$config->priority, \$config->argsCount);
                        }
                    } elseif (\$config->type === 'filter') {
                        if (!has_filter(\$config->name, [\$listenerInstance, 'handle'])) {
                            add_filter(\$config->name, [\$listenerInstance, 'handle'], \$config->priority, \$config->argsCount);
                        }
                    }
                }
            }
        }";
    }

    public static function generateServiceProviderManagerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Managers;\n\nuse {$pluginPrefix}\\Core\\Foundation\\ProviderManager;\nuse {$pluginPrefix}\\Core\\Support\\Traits\\IsSingleton;\n\nclass ServiceProviderManager extends ProviderManager\n{\n    use IsSingleton;\n\n    protected array \$providers = [];\n}";
    }

}