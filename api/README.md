## Official Documentation

Documentation for the framework can be found on the [Lumen website](http://lumen.laravel.com/docs).

## Composer

To install the required packages simply run `composer install` inside the `api` directory.

## Deployment

Move everything except the `public` folder to a non public directory. Move the contents of `public` to your public directory. Modify the index.php` to change the application path.

## Configuration

```
cd api/
mv .env.example .env
nano .env
```

* ```APP_ENV=production```
* ```APP_DEBUG=false```
* ```APT_KEY=[random 32 char string]```

You can generate an app key using PHP's ```str_random(32)```.

* ```DB_CONNECTION=mysql```
* ```DB_HOST=[host]```
* ```DB_PORT=[port]```
* ```DB_DATABASE=schedule```
* ```DB_USERNAME=[username]```
* ```DB_PASSWORD=[password]```
* ```CACHE_DRIVER=file```
* ```QUEUE_DRIVER=sync```


## Running Migrations

To run all of your outstanding migrations, execute the migrate Artisan command.

```
php artisan migrate
```