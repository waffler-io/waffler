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

## Commands:
This package exposes a few commands to generate the client interface classes.

### `php artisan waffler:cache`
Generates the client interface classes declared in the `waffler.php` config file.

This command is also automatically called when you run `php artisan optimize`, and the generated class names are saved alongside the optimized cache.

### `php artisan waffler:clear`
Clears the generated client interface classes.

This command is also automatically called when you run `php artisan optimize:clear`.

## Important tip:
While developing, it is recommended to avoid caching the application config files using `php artisan config:cache` or `php artisan optimize`.

This package uses the configuration files to save the generated class name, and the name can change when you are editing the source code of the client interface.

Only cache the config files when everything is ready to be deployed, like when you (are supposed to) do in production.

If you encounter a generated class that has missing methods, it is probably because the config file has been cached. Try clearing the cache and regenerating the config files.
