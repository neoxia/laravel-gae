[![Build Status](https://travis-ci.org/neoxia/laravel-gae.svg?branch=master)](https://travis-ci.org/neoxia/laravel-gae)
[![Coverage Status](https://coveralls.io/repos/github/neoxia/laravel-gae/badge.svg?branch=master)](https://coveralls.io/github/neoxia/laravel-gae?branch=master)

## Laravel GAE

This packages provides helpers and drivers in order to deploy a Laravel application to Google App Engine. 
It helps building the application and connects to Google Cloud Storage.

## Installation

In order to install this package, you have to add `neoxia/laravel-csv-response` in your `composer.json`.

```JSON
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/neoxia/laravel-gae"
    }
],
"require": {
    "neoxia/laravel-gae": "master"
},
```

And add the service providers in `config/app.php`.

```PHP
Neoxia\GAE\Console\GAEConsoleServiceProvider::class,
Neoxia\GAE\Filesystem\GAEFilesystemServiceProvider::class,
Neoxia\GAE\Migrations\GAEMigrationsServiceProvider::class,
```

## Usage

When building your application, run `gae:compile-views` and `gae:compile-app-file`.
You need a `app.blade.yaml` file at the root of your project. The variables sent to this view come
from the builder environment variables.

You can specify several targets for different builds, and some environment variables will be
parsed in accordance to the specified target. For example, if you specify `--target=master`,
the variable called `MASTER__APP_DEBUG` will replace the `APP_DEBUG`. This is useful when all
your configuration is in your environment for security.

You also need call the migrations route, which will run all the migrations for your database.
It will also create the database if it doesn't exist. You can call this routes by using curl :

```BASH
curl --data "token=YOUR_TOKEN" https://[VERSION-dot-][SERVICE-dot-]PROJECT_ID.appspot.com/migrate
```
