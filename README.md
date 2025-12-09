## Requisitos
- PHP 8.3+
- MySQL 8
- Composer
- Laravel 12
- Laravel Passport
- Docker y Docker Compose
- Pest

## Clonar Repositorios
git clone https://github.com/MartinCalozo/mds-eventos.git
cd mds-eventos

## Copiar el archivo .env y poner sus variables correspondientes
cp .env.example .env
cp .env.testing.example .env.testing

## Modificar entornos
### Reemplazar en .env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mds-events
DB_USERNAME=mds_user
DB_PASSWORD=pass
DB_ROOT_PASSWORD="45zY>3hX|P/"

PASSPORT_PUBLIC_KEY=/var/www/html/storage/oauth/oauth-public.key
PASSPORT_PRIVATE_KEY=/var/www/html/storage/oauth/oauth-private.key

PASSPORT_CLIENT_ID=
PASSPORT_CLIENT_SECRET=

INVITATIONS_API_URL="https://mds-events-main-nfwvz9.laravel.cloud/api/invitations"
INVITATIONS_API_TOKEN=secret123
INVITATIONS_API_FALLBACK=false

### Reemplazar en .env.testing
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mds_events_test
DB_USERNAME=mds_user
DB_PASSWORD=pass
DB_ROOT_PASSWORD="45zY>3hX|P/"

PASSPORT_PUBLIC_KEY=/var/www/html/storage/testing_oauth/oauth-public.key
PASSPORT_PRIVATE_KEY=/var/www/html/storage/testing_oauth/oauth-private.key

PASSPORT_CLIENT_ID=
PASSPORT_CLIENT_SECRET=

INVITATIONS_API_URL="https://mds-events-main-nfwvz9.laravel.cloud/api/invitations"
INVITATIONS_API_TOKEN=secret123
INVITATIONS_API_FALLBACK=true
<!-- Esto permite testear sin depender de la API real, garantizando consistencia -->

## Levantar los servicios de Docker
docker compose up -d --build


## Instalacion, Migracion y Passport
composer require laravel/passport

docker exec -it mds_app bash
    php artisan key:generate
    php artisan migrate
    php artisan passport:install
        [yes]
        [yes]
        Cuando pregunte: Which user provider should this client use to retrieve users? hay que poner "users" (sin comillas)

    php artisan passport:client --password
        Client
        users

        Passport devuelve 
        Client ID ........................................................................................... XXXXXXXXXXXXXXXXXXXXXXXXXX
        Client Secret ........................................................................................... XXXXXXXXXXXXXXXXXXXXXXXXXX

        Copiar y Pegarlo en el .env en las variables con los siguientes nombre
        PASSPORT_CLIENT_ID
        PASSPORT_CLIENT_SECRET
    exit

## Repetir proceso en mds_test
docker exec -it mds_test bash
    php artisan key:generate
    php artisan migrate

    php artisan passport:client --password
        ClientTest
        users

        Passport devuelve 
        Client ID ........................................................................................... XXXXXXXXXXXXXXXXXXXXXXXXXX
        Client Secret ........................................................................................... XXXXXXXXXXXXXXXXXXXXXXXXXX

        Copiar y Pegarlo en el .env.testing en las variables con los siguientes nombre
        PASSPORT_CLIENT_ID
        PASSPORT_CLIENT_SECRET

    php artisan db:seed
    exit

## Postman
Abrir Postman, arriba a la izquierda al lado del boton "new" hay otro que es "import"
Clickeo "import"
Arrastro el archivo que se encuentra en la carpeta postman y lo importo

Cuando veas en una url "{{baseURL}}" apoyá el mouse arriba y pegá la siguiente línea "http://localhost:8000"(sin comillas)

En el Login te devuelve un access_token
En el caso de loguearte con un checker.
Ir al que dice "requiere Bearer CHECKER" entre los títulos y poner en header (abajo de la url)
Key = "Authorization" y Value = "Bearer {access_token}".
Hacer lo mismo en los que dice "requiere Bearer ADMIN" pero tomando el access_token del usuario admin, ejemplo para el login "admin@mds.com".
Y hacer lo mismo "requiere Bearer SECRET" y poner value = "Bearer secret123"

Para el "Validar Ticket" hay que buscar en la base de datos (http://localhost:8080/)
usuario: mds_user
contraseña: pass
Ir a la base de datos mds-events, a la tabla tickets y copiar y pegar un "code", ese se pone en la url /api/tickets/{code}/validate

En obtener invitacion se ponen al final de la url los hashes que me envíaron ustedes por ejemplo a8f22d, a8f22e, a8f22f, b9g33e, b9g33f, b9g33g

## Job
Está corriendo contantemente en un contenedor de docker llamado mds_queue

## Testear
docker exec -it mds_test bash
    php artisan test --coverage
    exit

Las Instalaciones de XDebug están dentro del Dockerfile

# Problemas
Si tienen algún problema con passport por favor enviarme mail (martincalozo@gmail.com) o contactarse conmigo por otro medio, porque passport no me permitía borrar o pisar.
Entonces hay que eliminar de manera manual las tablas en la base de datos, las migraciones en el código y despues volver a correr los comando.

## Resumen de Endpoints

### Públicos
GET /api/invitations/{hash}
Consulta una invitación por hash utilizando la API externa

POST /api/redeem 
Procesa la redención de una invitación. Este proceso se encola y se ejecuta en segundo plano

### Autenticación
POST /api/auth/register-checker
Registra un usuario tipo checker

POST /api/auth/login
Inicia sesión y devuelve un access token

POST /api/auth/logout
Cierra sesión (requiere autenticación)

### Checker (requiere Bearer Token de checker)
POST /api/tickets/{code}/validate
Valida un ticket por código

### Admin (requiere Bearer Token de admin)
GET /api/admin/events/{event}/tickets-used
Lista tickets utilizados por evento, con paginación

GET /api/admin/redemptions
Lista las redenciones realizadas en el sistema

## Estrategia con la API Externa
Timeout de 5 segundos para evitar que quede colgado
Manejo de errores cuando la API devuelve 4xx/5xx
Modo fallback configurable desde .env (sirve para testing)
Toda la lógica va en InvitationService
En los tests uso Http::fake() para no depender de la API real

## Decisiones Técnicas
Laravel 12 + Passport para auth con roles (admin y checker)
InvitationService para separar la integración externa
Redenciones procesadas en segundo plano con un Job y cola (ProcessRedemption y contenedor: mds_queue)
Evitar usar N+1
Instalar XDebug desde Dockerfile