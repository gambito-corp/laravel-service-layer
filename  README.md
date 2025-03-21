### 1. Agregar el repositorio local
En el archivo `composer.json` de tu proyecto Laravel, agrega el siguiente bloque para incluir el paquete como un repositorio local:


"repositories": [
{
"type": "path",
"url": "../packages/laravel-service-layer",
"options": {
"symlink": true
}
}
],
"require": {
"gambito-corp/laravel-service-layer": "@dev"
}


### 2. Instalar el paquete
Ejecuta el siguiente comando en la terminal desde la raíz de tu proyecto Laravel:

composer update


### 3. Publicar los stubs
Si deseas personalizar los archivos stub utilizados por el paquete, publícalos con el siguiente comando:

php artisan vendor:publish --tag=laravel-service-layer-stubs

Esto copiará los stubs a la carpeta `stubs/` de tu proyecto.

---

## Uso

El paquete incluye los siguientes comandos Artisan:

### 1. Crear todos los componentes relacionados con un modelo

php artisan make:all {name} [--options]

**Opciones disponibles:**
- `--m|migration`: Crear migración.
- `--f|factory`: Crear fábrica.
- `--s|seed`: Crear seeder.
- `--c|controller`: Crear controlador.
- `--l|livewire`: Crear componente Livewire.
- `--r|resource`: Crear controlador tipo recurso.
- `--sl|service-layer`: Crear capa de servicio (interface, repository y service).
- `--a|all`: Crear todos los componentes.


---

## Estructura generada

Al ejecutar los comandos, se generará automáticamente una estructura organizada en tu proyecto Laravel:

app/
├── Interfaces/
│ └── Product/
│ └── ProductInterface.php
├── Repositories/
│ └── Product/
│ └── ProductRepository.php
└── Services/
└── Product/
└── ProductService.php


---

## Contribución

Si deseas contribuir al desarrollo del paquete, por favor abre un pull request o reporta problemas en el repositorio oficial.

---

## Licencia

Este paquete está licenciado bajo la [MIT License](LICENSE).

