<?php

namespace NikoGin\Services\Logic;

class RepositoryLogicGenerator
{
    /**
     * @param string $name          Base name (e.g. "exampleTable")
     * @param string $table         Table name (e.g. "example_table")
     * @param string $repoDir       Directory where the file should go
     * @param string $pluginPrefix  Plugin prefix (e.g. "ET")
     */
    public function generate(string $name, string $table, string $repoDir, string $pluginPrefix): void
    {
        $filePath = "$repoDir/$name.php";

        // Ensure the directory exists
        if (!file_exists($repoDir)) {
            mkdir($repoDir, 0777, true);
        }

        if (!file_exists($filePath)) {
            $content = $this->generateRepositoryClass($name, $table, $pluginPrefix);
            file_put_contents($filePath, $content);
        }
    }

    /**
     * Build the repository class code.
     */
    private function generateRepositoryClass(string $name, string $table, string $pluginPrefix): string
    {
        $className      = $name . 'Repository';
        $namespace      = $pluginPrefix . '\Http\Repositories';
        $baseRepository = $pluginPrefix . '\Core\Foundation\Repository';

        return <<<PHP
<?php

namespace {$namespace};

use {$baseRepository};

class {$className} extends Repository
{
    public function __construct()
    {
        parent::__construct('{$table}');
    }
}
PHP;
    }
}
