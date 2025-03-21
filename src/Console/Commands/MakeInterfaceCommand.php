<?php

namespace GambitoCorp\LaravelServiceLayer\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeInterfaceCommand extends Command
{
    protected $signature = 'make:interface {name} {--f|force : Sobrescribir archivo existente}';
    protected $description = 'Crea una nueva interfaz en app/Interfaces';

    public function handle()
    {
        $fs = new Filesystem();
        $name = $this->argument('name');
        $stubPath = base_path('stubs/interface.stub');

        // Obtener path y nombre de clase
        $path = Str::beforeLast($name, '/');
        $path = Str::replaceLast('Interface', '', $path); // Eliminar "Interface" del path
        $className = Str::afterLast($name, '/');

        // Forzar sufijo Interface si no existe
        if (!Str::endsWith($className, 'Interface')) {
            $className .= 'Interface';
        }

        // Construir namespace correctamente
        $namespace = empty($path)
            ? 'App\\Interfaces'
            : 'App\\Interfaces\\'.Str::replace('/', '\\', $path);

        // Ruta del archivo
        $filePath = app_path(
            "Interfaces/".
            Str::replace('\\', '/', $path) .
            "/{$className}.php"
        );

        if ($fs->exists($filePath) && !$this->option('force')) {
            $this->error("La interfaz {$className} ya existe!");
            return;
        }

        $fs->ensureDirectoryExists(dirname($filePath));

        // Generar contenido
        $stub = $fs->get($stubPath);
        $content = Str::of($stub)
            ->replace('{{ namespace }}', $namespace)
            ->replace('{{ class }}', $className);

        $fs->put($filePath, $content);

        $this->info("Interfaz creada: {$className} en {$filePath}");
    }
}
