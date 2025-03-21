<?php

namespace GambitoCorp\LaravelServiceLayer\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name} {--f|force : Sobrescribir archivo existente}';
    protected $description = 'Crea un nuevo servicio en app/Services';

    public function handle()
    {
        $fs = new Filesystem();
        $name = $this->argument('name');
        $stubPath = base_path('stubs/service.stub');

        $path = Str::beforeLast($name, '/');
        $path = Str::replaceLast('Service', '', $path); // Eliminar "Service" del path
        $className = Str::afterLast($name, '/');

        // Forzar sufijo Service si no existe
        if (!Str::endsWith($className, 'Service')) {
            $className .= 'Service';
        }

        // Construir namespace
        $namespace = 'App\Services';
        if (!empty($path)) {
            $namespace .= '\\' . Str::replace('/', '\\', $path);
        }

        // Generar ruta del archivo
        $filePath = app_path(
            "Services/" .
            ($path ? $path . '/' : '') .
            "{$className}.php"
        );

        if ($fs->exists($filePath) && !$this->option('force')) {
            $this->error("El servicio {$className} ya existe!");
            return;
        }



        $fs->ensureDirectoryExists(dirname($filePath));

        $stub = $fs->get($stubPath);
        $content = Str::of($stub)
            ->replace('{{ namespace }}', $namespace)
            ->replace('{{ interface }}',  $path . '\\' . $path . 'Interface')
            ->replace('{{ class }}', $className);

        $fs->put($filePath, $content);

        $this->info("Servicio creado: {$className} en {$filePath}");
    }
}
