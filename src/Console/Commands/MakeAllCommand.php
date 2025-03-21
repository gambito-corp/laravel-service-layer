<?php

namespace GambitoCorp\LaravelServiceLayer\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeAllCommand extends Command
{
    protected $signature = 'make:all {name}
                            {--m|migration : Create migration}
                            {--f|factory : Create factory}
                            {--s|seed : Create seeder}
                            {--c|controller : Create controller}
                            {--l|livewire : Create Livewire component}
                            {--r|resource : Create resource controller}
                            {--sl|service-layer : Create service layer}
                            {--a|all : Create all components}';

    protected $description = 'Create all components for a model';

    protected function ensureBindServiceProviderExists()
    {
        $providerPath = app_path('Providers/BindServiceProvider.php');

        if (!file_exists($providerPath)) {
            $contents =
<<<PHP
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BindServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bindings se registrarán aquí dinámicamente
    }

    public function provides()
    {
        return [];
    }
}
PHP;
            file_put_contents($providerPath, $contents);
            $this->info("✅ BindServiceProvider creado en app/Providers");
        }
    }

    public function handle()
    {
        $name = $this->argument('name');
        $studlyName = Str::studly($name);
        $path = Str::replace('\\', '/', $studlyName);
        $options = $this->options();

        // Handle --all option
        if ($this->option('all')) {
            $options = array_merge($options, [
                'migration' => true,
                'factory' => true,
                'seed' => true,
                'controller' => true,
                'livewire' => true,
                'resource' => false,
                'service-layer' => true
            ]);
        }
        $this->ensureBindServiceProviderExists();

        // 1. Create model with related components
        $this->call('make:model', [
            'name' => $studlyName,
            '--migration' => $options['migration'],
            '--factory' => $options['factory'],
            '--seed' => $options['seed']
        ]);

        // 2. Create controller
        if ($options['controller'] || $options['resource']) {
            $controllerOptions = [];
            if ($options['resource']) {
                $controllerOptions['--resource'] = true;
            }

            $this->call('make:controller', [
                    'name' => "{$path}/{$studlyName}Controller",
                ] + $controllerOptions);
        }

        // 3. Create Livewire component
        if ($options['livewire']) {
            $this->call('make:livewire', [
                'name' => "{$studlyName}Table"
            ]);
        }

        // 4. Create service layer
        if ($options['service-layer']) {
            $this->createServiceLayer($studlyName, $path);
        }

        // 5. Add resource route
        if ($options['resource']) {
            $this->addResourceRoute($studlyName);
        }

        $this->info("✅ All components for {$studlyName} created successfully!");
        $this->listComponents($studlyName, $path);
    }

    protected function createServiceLayer($studlyName, $path)
    {
        // Create interface
        $this->call('make:interface', [
            'name' => "{$studlyName}Interface"
        ]);
        // Create repository
        $this->call('make:repository', [
            'name' => "{$studlyName}Repository"
        ]);
        // Registrar binding en AppServiceProvider
        $this->registerBinding(
            $path
        );
        // Create service
        $this->call('make:service', [
            'name' => "{$studlyName}Service"
        ]);
    }

    protected function registerBinding($path)
    {
        $interface = "App\\Interfaces\\{$path}\\{$path}Interface";
        $repository = "App\\Repositories\\{$path}\\{$path}Repository";

        $providerPath = app_path('Providers/BindServiceProvider.php');
        $contents = File::get($providerPath);

        // Añadir namespaces necesarios
        $contents = $this->addUseStatement($contents, $interface);
        $contents = $this->addUseStatement($contents, $repository);

        // Actualizar método register()
        $bindingCode = <<<PHP

            \$this->app->bind({$path}Interface::class, {$path}Repository::class);
    PHP;

        // Verificar si ya existe el binding
        if (!Str::contains($contents, $bindingCode)) {
            // Encontrar la posición correcta para insertar el binding
            $contents = Str::replaceFirst(
                '// Bindings se registrarán aquí dinámicamente',
                "   {$bindingCode}\n        // Bindings se registrarán aquí dinámicamente",
                $contents
            );
            // Guardar los cambios en el archivo
            File::put($providerPath, $contents);
            $this->info("✅ Binding registrado en BindServiceProvider");
        }

        // Actualizar método provides()
        $providesCode = <<<PHP
        return [
            '{$path}Interface'::class,

    PHP;

        if (!Str::contains($contents, $providesCode)) {
            $contents = Str::replaceFirst(
                'return [',
                "return [\n            {$path}Interface::class,",
                $contents
            );
        }

        File::put($providerPath, $contents);
        $this->info("✅ Binding registrado en BindServiceProvider");
    }

    protected function addUseStatement($contents, $class)
    {
        $useStatement = "use {$class};";

        if (!Str::contains($contents, $useStatement)) {
            return Str::replaceFirst(
                'namespace App\Providers;',
                "namespace App\Providers;\n\n{$useStatement}",
                $contents
            );
        }

        return $contents;
    }


    protected function addResourceRoute($studlyName)
    {
        $route = "\nRoute::resource('" . Str::kebab($studlyName) . "', \\App\\Http\\Controllers\\{$studlyName}\\{$studlyName}Controller::class);";

        try {
            File::append(base_path('routes/web.php'), $route);
            $this->info("✅ Resource route added for {$studlyName}");
        } catch (\Exception $e) {
            $this->error("Failed to add route: " . $e->getMessage());
        }
    }

    protected function listComponents($studlyName, $path)
    {
        $this->line("\nCreated components:");
        $this->line("👉 Model: app/Models/{$studlyName}.php");

        if ($this->option('migration')) {
            $this->line("👉 Migration: database/migrations/*_create_".Str::snake($studlyName)."_table.php");
        }

        if ($this->option('factory')) {
            $this->line("👉 Factory: database/factories/{$studlyName}Factory.php");
        }

        if ($this->option('seed')) {
            $this->line("👉 Seeder: database/seeders/{$studlyName}Seeder.php");
        }

        if ($this->option('controller') || $this->option('resource')) {
            $this->line("👉 Controller: app/Http/Controllers/{$path}/");
        }

        if ($this->option('livewire')) {
            $this->line("👉 Livewire: app/Livewire/{$path}/");
        }

        if ($this->option('service-layer')) {
            $this->line("👉 Interface: app/Interfaces/{$path}/");
            $this->line("👉 Repository: app/Repositories/{$path}/");
            $this->line("👉 Service: app/Services/{$path}/");
        }
    }

}
