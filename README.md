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

## Siguientes pasos naturales

- crear migraciones para clubes, jugadores y competiciones
- definir modelos Eloquent y relaciones
- exponer controladores REST para Angular
- anadir validaciones y seeders de ejemplo
