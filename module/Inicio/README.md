# Módulo Inicio

Módulo principal del sistema QR Vehículos que maneja la página de inicio y las funcionalidades principales.

## Estructura

```
module/Inicio/
├── config/
│   └── module.config.php          # Configuración de rutas y servicios
├── src/
│   ├── Module.php                 # Clase principal del módulo
│   └── Controller/
│       ├── IndexController.php    # Controlador principal
│       └── Factory/
│           └── IndexControllerFactory.php
└── view/
    └── inicio/
        └── index/
            └── index.phtml        # Vista principal
```

## Rutas configuradas

- **`/`** → `Inicio\Controller\IndexController::indexAction()`
  - Página principal del sistema

- **`/inicio[/:action[/:id]]`** → `Inicio\Controller\IndexController`
  - Rutas dinámicas para el módulo Inicio
  - Ejemplos: `/inicio/ver/1`, `/inicio/editar/5`

## Uso

Este módulo utiliza el layout definido en el módulo `Application`, por lo que:
- Navbar, footer y estilos base vienen de `Application/view/layout/layout.phtml`
- Solo necesitas definir el contenido específico en las vistas de este módulo

## Agregar nuevas acciones

Para agregar una nueva acción al controlador:

```php
// En IndexController.php
public function nuevaAction()
{
    return new ViewModel([
        'datos' => 'información'
    ]);
}
```

Y crear la vista correspondiente:
```
module/Inicio/view/inicio/index/nueva.phtml
```

## Agregar nuevos controladores

1. Crear el controlador en `src/Controller/`
2. Crear su factory en `src/Controller/Factory/`
3. Registrar en `config/module.config.php`:

```php
'controllers' => [
    'factories' => [
        Controller\NuevoController::class => Controller\Factory\NuevoControllerFactory::class,
    ],
],
```

4. Agregar ruta si es necesario
