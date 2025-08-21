# READ-ONLY
This is a readonly subsplit tree of [The Waffler Project](https://github.com/waffler-io/waffler).

If you want to contribute, please visit the main repository.

---

## Installation
To install this library, run the following command:

```shell
composer require waffler/laravel-bridge

php artisan vendor:publish --tag="waffler-config"
```

## How to configure:
This package exposes a `waffler.php` config file to register your
client interfaces into the application [service container](https://laravel.com/docs/8.x/container).

### The `clients` array:
Register your clients in the service container.
```php
'clients' => [
    App\Clients\MyClientInterface::class => [/* GuzzleHttp options */],
],
```

### The `aliases` array:
Give an alias to your clients.
```php
'aliases' => [
    App\Clients\MyClientInterface::class => 'my-custom-alias',
],
```

### The `global_options` array:
An array of guzzle http options to be used in all client instances.
```php
'global_options' => [/* GuzzleHttp options */],
```

### The `singletons` array:
An array of clients to be registered as singletons.
```php
'singletons' => [
    App\Clients\MyClientInterface::class,
],
```
