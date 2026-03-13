<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'api:make-module {name}';

    protected $description = 'Create a new API module scaffold';

    public function handle(): int
    {
        $input = trim((string) $this->argument('name'));

        if ($input === '') {
            $this->error('Module name is required.');

            return self::FAILURE;
        }

        $replacements = $this->buildReplacements($input);
        $modulePath = base_path('modules/' . ucfirst($replacements['module']));

        if (File::exists($modulePath)) {
            $this->error("Module [{$replacements['module']}] already exists.");

            return self::FAILURE;
        }

        $this->createDirectories($modulePath);

        $this->writeStub($modulePath . '/module.json', 'module.json.stub', $replacements);
        $this->writeStub($modulePath . '/config/module.php', 'config.module.stub', $replacements);
        $this->writeStub($modulePath . '/config/permissions.php', 'config.permissions.stub', $replacements);
        $this->writeStub($modulePath . '/routes/api.php', 'routes.api.stub', $replacements);
        $this->writeStub(
            $modulePath . '/src/Providers/' . $replacements['moduleStudly'] . 'ServiceProvider.php',
            'service-provider.stub',
            $replacements
        );
        $this->writeStub(
            $modulePath . '/tests/' . $replacements['moduleStudly'] . 'ModuleTest.php',
            'test.stub',
            $replacements
        );

        $this->info("Module [{$replacements['moduleStudly']}] created successfully.");
        $this->line("Path: {$modulePath}");

        return self::SUCCESS;
    }

    protected function buildReplacements(string $input): array
    {
        $module = Str::of($input)->kebab()->lower()->toString();
        $moduleStudly = Str::studly($input);

        return [
            'module' => $module,
            'moduleStudly' => $moduleStudly,
            'moduleTitle' => Str::headline($input),
        ];
    }

    protected function createDirectories(string $modulePath): void
    {
        $directories = [
            $modulePath,
            $modulePath . '/config',
            $modulePath . '/routes',
            $modulePath . '/src',
            $modulePath . '/src/Providers',
            $modulePath . '/database',
            $modulePath . '/database/migrations',
            $modulePath . '/tests',
        ];

        foreach ($directories as $directory) {
            File::ensureDirectoryExists($directory);
        }
    }

    protected function writeStub(string $targetPath, string $stubName, array $replacements): void
    {
        File::put($targetPath, $this->renderStub($stubName, $replacements));
    }

    protected function renderStub(string $stubName, array $replacements): string
    {
        $content = File::get($this->stubPath($stubName));

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }

        return $content . (str_ends_with($content, PHP_EOL) ? '' : PHP_EOL);
    }

    protected function stubPath(string $stubName): string
    {
        return base_path('stubs/module/' . $stubName);
    }
}
