[![Build Status](https://travis-ci.org/neoxia/laravel-csv-response.svg?branch=master)](https://travis-ci.org/neoxia/laravel-csv-response)
[![Coverage Status](https://coveralls.io/repos/github/neoxia/laravel-csv-response/badge.svg?branch=master)](https://coveralls.io/github/neoxia/laravel-csv-response?branch=master)

## Laravel GAE

This packages provides helpers and drivers in order to deploy a Laravel application to Google App Engine. It helps to build the application and to connect to Google Cloud Storage.

## Installation

In order to install this package, you have to add `neoxia/laravel-csv-response` in your `composer.json`.

```JS
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
```
