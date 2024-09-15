<p align="center">
    <img width="400px" src="logo.gif">
</p>

<p align="center">
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver/v/stable"></a>
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver/downloads"></a>
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver/license"></a>
    <a href="https://github.com/babenkoivan/elastic-scout-driver-plus/actions?query=workflow%3ATests"><img src="https://github.com/babenkoivan/elastic-scout-driver-plus/workflows/Tests/badge.svg"></a>
    <a href="https://github.com/babenkoivan/elastic-scout-driver/actions?query=workflow%3A%22Code+style%22"><img src="https://github.com/babenkoivan/elastic-scout-driver/workflows/Code%20style/badge.svg"></a>
    <a href="https://github.com/babenkoivan/elastic-scout-driver/actions?query=workflow%3A%22Static+analysis%22"><img src="https://github.com/babenkoivan/elastic-scout-driver/workflows/Static%20analysis/badge.svg"></a>
    <a href="https://paypal.me/babenkoi"><img src="https://img.shields.io/badge/donate-paypal-blue"></a>
</p>

<p align="center">
    <a href="https://ko-fi.com/ivanbabenko" target="_blank"><img src="https://ko-fi.com/img/githubbutton_sm.svg" alt="Support the project!"></a>
</p>

---

Elasticsearch driver for Laravel Scout.

## Contents

* [Compatibility](#compatibility)
* [Installation](#installation) 
* [Configuration](#configuration)
* [Basic Usage](#basic-usage)
* [Advanced Search](#advanced-search)
* [Migrations](#migrations)
* [Pitfalls](#pitfalls)

## Compatibility

The current version of Elastic Scout Driver has been tested with the following configuration:

* PHP 7.4-8.x
* Elasticsearch 8.x
* Laravel 7.x-10.x
* Laravel Scout 7.x-10.x

## Installation

The library can be installed via Composer:

```bash
composer require babenkoivan/elastic-scout-driver
```

**Note**, that this library is just a driver for Laravel Scout, don't forget to install it beforehand:
```bash
composer require laravel/scout
``` 

After Scout has been installed, publish its configuration file using:

```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

Then, change the `driver` option in the `config/scout.php` file to `elastic`:

```php
// config/scout.php

'driver' => env('SCOUT_DRIVER', 'elastic'),
```

If you want to use Elastic Scout Driver with [Lumen framework](https://lumen.laravel.com/) check [this guide](https://github.com/babenkoivan/elastic-scout-driver/wiki/Lumen-Installation).

## Configuration

Elastic Scout Driver uses [babenkoivan/elastic-client](https://github.com/babenkoivan/elastic-client) as a dependency.
To change the client settings you need to publish the configuration file first:

```bash
php artisan vendor:publish --provider="Elastic\Client\ServiceProvider"
```

In the newly created `config/elastic.client.php` file you can define the default connection name using configuration hashes. 
Please, refer to the [elastic-client documentation](https://github.com/babenkoivan/elastic-client) for more details.

Elastic Scout Driver itself has only one configuration option at the moment - `refresh_documents`. 
If it's set to `true` (`false` by default) documents are indexed immediately, which might be handy for testing.   

You can configure `refresh_documents` in the `config/elastic.scout_driver.php` file after publishing it with the following command:

```bash
php artisan vendor:publish --provider="Elastic\ScoutDriver\ServiceProvider"
``` 

At last, do not forget, that with Scout you can configure the searchable data, the model id and the index name.
Check [the official Scout documentation](https://laravel.com/docs/master/scout#configuration) for more details.

> Note, that the `_id` field can't be part of the searchable data, so make sure the field is excluded or renamed 
> in the `toSearchableArray` method in case you are using MongoDB as the database.

## Basic usage

Elastic driver uses Elasticsearch [query string](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html)
wrapped in a [bool query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html) 
under the hood. It means that you can use [mini-language syntax](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html#query-string-syntax)
when searching a model:

```php
$orders = App\Order::search('title:(Star OR Trek)')->get();
```

When the query string is omitted, the [match all query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html) 
is used:
```php
$orders = App\Order::search()->where('user_id', 1)->get();
``` 

Please refer to [the official Laravel Scout documentation](https://laravel.com/docs/master/scout)
for more details and usage examples.

## Advanced Search

In case the basic search doesn't cover your project needs check [Elastic Scout Driver Plus](https://github.com/babenkoivan/elastic-scout-driver-plus),
which extends standard Scout search capabilities by introducing advanced query builders. These builders give you 
possibility to use compound queries, custom filters and sorting, highlights and more.

## Migrations

If you are looking for a way to control Elasticsearch index schema programmatically check [Elastic Migrations](https://github.com/babenkoivan/elastic-migrations).
Elastic Migrations allow you to modify application's index schema and share it across multiple environments with the same ease, 
that gives you Laravel database migrations.

## Pitfalls

There are few things, which are slightly different from other Scout drivers:
* As you probably know, Scout only indexes fields, which are returned by the `toSearchableArray` method. 
Elastic driver indexes a model even when `toSearchableArray` returns an empty array. You can change this behaviour by 
overwriting the `shouldBeSearchable` method of your model:
```php
public function shouldBeSearchable()
{
    return count($this->toSearchableArray()) > 0;
}
```
* Raw search returns an instance of `SearchResult` class (see [Elastic Adapter](https://github.com/babenkoivan/elastic-adapter#search)):
```php
$searchResult = App\Order::search('Star Trek')->raw();
``` 
* To be compatible with other drivers and to not expose internal implementation of the engine, Elastic driver ignores callback
parameter of the `search` method:
```php
App\Order::search('Star Trek', function () {
    // this will not be triggered
})->get()
```
