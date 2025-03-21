# Laravel Service Layer

**Laravel Service Layer** es un paquete diseñado para facilitar la implementación de una arquitectura de capa de servicio en aplicaciones Laravel. Este paquete incluye comandos para generar interfaces, repositorios y servicios, simplificando la creación de componentes organizados y promoviendo la separación de responsabilidades.

---

## Instalación

### 1. Instalar el paquete desde Packagist
Ejecuta el siguiente comando en la terminal desde la raíz de tu proyecto Laravel:

composer require gambito-corp/laravel-service-layer

`composer require gambito-corp/laravel-service-layer`

### 2. Publicar los stubs
Si deseas personalizar los archivos stub utilizados por el paquete, publícalos con el siguiente comando:

`php artisan vendor:publish --tag=laravel-service-layer-stubs`

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

## Contribución

Si deseas contribuir al desarrollo del paquete, por favor abre un pull request o reporta problemas en el repositorio oficial.

---

## Licencia

Este paquete está licenciado bajo la [MIT License](LICENSE).

