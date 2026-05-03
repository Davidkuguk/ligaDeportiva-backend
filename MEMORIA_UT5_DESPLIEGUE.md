# Memoria UT5 - Despliegue Backend Laravel

## Objetivo

En esta unidad se prepara el backend Laravel de la Liga Deportiva del IES Maestre de Calatrava para ejecutarse en produccion mediante Render y para desplegarse automaticamente desde GitHub Actions.

## Servicio Publicado

- Backend: https://liga-mestre-api.onrender.com/
- Endpoint de salud: https://liga-mestre-api.onrender.com/api/health

## Plataforma

El backend se despliega como servicio web Docker en Render. La imagen usa Nginx con PHP-FPM, publica la carpeta `public` de Laravel y ejecuta automaticamente:

- instalacion de dependencias PHP sin paquetes de desarrollo;
- preparacion de carpetas escribibles de Laravel;
- migraciones con `php artisan migrate --force`;
- seeders con `php artisan db:seed --force`.

## Configuracion Render

El archivo `render.yaml` define la infraestructura del backend:

- tipo de servicio: `web`;
- runtime: `docker`;
- ruta de salud: `/api/health`;
- base de datos PostgreSQL gestionada por Render;
- variables de entorno de produccion.

Variables principales configuradas en Render:

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://liga-mestre-api.onrender.com
FRONTEND_URLS=https://ligadeportivafront.netlify.app,https://liga-deportiva-front.onrender.com,http://localhost:4200,http://127.0.0.1:4200
DB_CONNECTION=pgsql
DATABASE_URL=gestionada por Render Postgres
LOG_CHANNEL=stderr
QUEUE_CONNECTION=sync
```

La variable `APP_KEY` se configura como secreto en Render y no se guarda en el repositorio. La cadena `DATABASE_URL` se inyecta desde la base de datos `liga-mestre-db` definida en el blueprint.

## Integracion Continua

El workflow `.github/workflows/tests.yml` valida el backend antes del despliegue:

- comprueba estilo con Laravel Pint;
- ejecuta pruebas unitarias;
- ejecuta pruebas Feature sobre la API;
- compila assets con Vite;
- prueba PHP 8.2, 8.3 y 8.4.

## Despliegue Continuo

El workflow `.github/workflows/deploy-render.yml` se ejecuta cuando el workflow `Tests` termina correctamente en `main` o `master`.

Para activarlo se crea en GitHub el secreto:

```text
RENDER_BACKEND_DEPLOY_HOOK_URL
```

Este secreto contiene la Deploy Hook URL del servicio de Render. Si las pruebas fallan, el despliegue no se lanza.

## Seguridad

- Las claves sensibles se gestionan como variables privadas en Render y GitHub Secrets.
- `APP_DEBUG` esta desactivado en produccion.
- CORS solo permite los origenes definidos en `FRONTEND_URLS`.
- El despliegue depende de CI, evitando publicar cambios con pruebas fallidas.
