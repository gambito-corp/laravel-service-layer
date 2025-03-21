<?php

namespace GambitoCorp\LaravelServiceLayer\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--f|force : Sobrescribir archivo existente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo repositorio en app/Repositories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fs = new Filesystem();
        $name = $this->argument('name');
        $stubPath = base_path('stubs/repository.stub');

        $path = Str::beforeLast($name, '/');
        $path = Str::replaceLast('Repository', '', $path); // Eliminar "Repository" del path
        $className = Str::afterLast($name, '/');

        // Forzar sufijo Repository si no existe
        if (!Str::endsWith($className, 'Repository')) {
            $className .= 'Repository';
        }

        // Construir namespace correctamente
        $namespace = 'App\Repositories';
        if (!empty($path)) {
            $namespace .= '\\'.Str::replace('/', '\\', $path);
        }

        // Construir ruta del archivo
        $filePath = app_path(
            "Repositories/".
            ($path ? $path.'/' : '').
            "{$className}.php"
        );

        if ($fs->exists($filePath) && !$this->option('force')) {
            $this->error("El repositorio {$className} ya existe!");
            return;
        }

        $fs->ensureDirectoryExists(dirname($filePath));

        $stub = $fs->get($stubPath);
        $content = Str::of($stub)
            ->replace('{{ namespace }}', $namespace)
            ->replace('{{ class }}', $className)
            ->replace('{{ model }}', $path)
            ->replace('{{ interface }}', $path.'Interface');

        $fs->put($filePath, $content);

        $this->info("Repositorio creado: {$className} en {$filePath}");
    }

}
