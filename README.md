## Requisitos
- PHP 8.3+
- MySQL 8
- Composer
- Laravel 12
- Laravel Passport
- Docker y Docker Compose

## Clonar Repositorios
git clone https://github.com/MartinCalozo/mds-eventos.git
cd mds-eventos

## Copiar el archivo .env y poner sus variables correspondientes
cp .env.example .env
php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mds-events
DB_USERNAME=mds_user
DB_PASSWORD=pass
DB_ROOT_PASSWORD="45zY>3hX|P)/"

## Levantar los servicios de Docker
docker compose up -d --build

## Instalacion, Migracion y Passport
composer require laravel/passport
docker exec -it mds_app bash
composer install
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
    Client ID
    Client Secret ........................................................................................... Or1AR0GsyieldhHIbFr18P6YU82bMUhhuoeL66O7

    Copiar y Pegarlo en el .env en las variables con los siguientes nombre
    PASSPORT_CLIENT_ID
    PASSPORT_CLIENT_SECRET
