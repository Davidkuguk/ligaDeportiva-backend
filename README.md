# Liga Deportiva Backend

Backend base en Laravel para la aplicacion web "Liga Deportiva del IES Maestre de Calatrava".

## Objetivo de esta fase

Este proyecto servira como base del backend para gestionar:

- clubes
- jugadores
- competiciones

La aplicacion queda preparada para evolucionar hacia una API RESTful que sera consumida mas adelante por el cliente Angular.

## Estado actual

En este entregable queda creada la estructura inicial de Laravel 12 con:

- configuracion base del proyecto
- entorno local preparado con SQLite
- ruta de salud de Laravel en `/up`
- ruta API inicial en `/api/health`

## Estructura principal

- `app/`: logica de la aplicacion
- `config/`: configuracion general
- `database/`: migraciones, seeders y base SQLite local
- `routes/`: rutas web, consola y API
- `tests/`: pruebas automaticas

## Estrategia de pruebas automatizadas

En este repositorio se ha automatizado la validacion del backend Laravel con tres niveles:

- pruebas unitarias para modelos y reglas de dominio del modulo `Jugadores`
- pruebas de integracion/feature sobre la API REST con base de datos en memoria
- pruebas E2E orientadas al backend, cubriendo el flujo HTTP completo del recurso `/api/jugadores`

La aplicacion cliente Angular no esta incluida en este workspace, asi que las pruebas del frontend y sus llamadas HTTP simuladas tendran que vivir en el repositorio del cliente cuando este disponible.

### Ejecutar la bateria de pruebas

```powershell
php artisan test
```

Si `php` no esta en el `PATH`, usa la ruta absoluta del ejecutable de tu entorno local.

### Integracion continua

GitHub Actions ejecuta automaticamente:

- estilo PHP con `Pint`
- suite de pruebas Laravel en PHP 8.2, 8.3 y 8.4
- compilacion de assets con `npm run build`

## Requisitos

- PHP 8.2 o superior
- Composer

En este equipo se ha usado `C:\xampp\php\php.exe` y un `composer.phar` local descargado en la raiz del workspace.

## Puesta en marcha

Desde la carpeta `ligaDeportiva-backend`:

```powershell
C:\xampp\php\php.exe ..\composer.phar install
Copy-Item .env.example .env
C:\xampp\php\php.exe -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
C:\xampp\php\php.exe artisan key:generate
C:\xampp\php\php.exe artisan migrate
C:\xampp\php\php.exe artisan serve
```

## Comprobacion rapida

- Aplicacion Laravel: `http://127.0.0.1:8000/up`
- API base: `http://127.0.0.1:8000/api/health`
