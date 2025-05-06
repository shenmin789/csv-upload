# Laravel Sample

This project runs with Laravel version 12.0

## Getting started

Assuming you've already installed on your machine: PHP (>= 7.0.0), [Laravel](https://laravel.com), [Composer](https://getcomposer.org) and [Node.js](https://nodejs.org).

``` bash
# install dependencies
composer install
npm install

# create .env file and generate the application key
cp .env.example .env
php artisan key:generate

# create the database with sqlite
php artisan migrate

# build CSS and JS assets
npm run build
```

launch the queue in another terminal

``` bash
php artisan queue:work -v
```

Then launch the server:

``` bash
composer run dev
```

The Laravel sample project is now up and running! Access it at http://localhost:8000.

