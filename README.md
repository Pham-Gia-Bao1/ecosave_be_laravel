## run server
--- php artisan serve

## install jwt
--- composer require tymon/jwt-auth

##  Publish the JWT Configuration
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

## Generate the JWT Secret Key
--- php artisan jwt:secret

## run migrations
--- php artisan migrate

## run seeders
--- php artisan db:seed
