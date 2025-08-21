![Build](https://github.com/waffler-io/waffler/actions/workflows/php-ci.yml/badge.svg)
[![License](https://img.shields.io/github/license/waffler-io/waffler)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/waffler/waffler.svg)](https://packagist.org/packages/waffler/waffler)

# #[Waffler]

<hr>

## Getting started
- This package requires PHP 8 or above.

### Installing standalone

```shell
$ composer require waffler/client
```

### [WIP] Installing in a Laravel project

```shell
$ composer require waffler/laravel-bridge
```

## Quick example

Let's imagine that we want to consume an API: `https://foo-bar.baz/api`

Our objectives are:

- Perform the login to retrieve the authorization token.
- Retrieve all posts from the database.

#### Step 1: Create the basic interface for your client.

```php
<?php // FooClient.php

namespace App\Clients;

interface FooClient
{
    /**
     * Retrieve authorization token.
     *
     * @param array $credentials Just pass the login and password.
     * @return array             The json response.
     */
    public function login(array $credentials): array;

    /**
     * Retrieve all posts.
     *
     * @param string $authToken The authorization token.
     * @param array $query      Some optional query string Filters.
     * @return array            The list of posts.
     */
    public function getPosts(string $authToken, array $query = []): array;
}
```

#### Step 2: Annotate the methods with Waffler Attributes.

The magic is almost done. Now we need to annotate the methods and parameters to "teach" Waffler how to make the
requests. There are dozens of Attributes, but for this example we just need 5 of them.

Import the Attributes from the `Waffler\Component\Attributes` namespace.

```php
<?php // FooClient.php

namespace App\Clients;

use Waffler\Component\Attributes\Auth\Bearer;
use Waffler\Component\Attributes\Request\Json;
use Waffler\Component\Attributes\Request\Query;
use Waffler\Component\Attributes\Verbs\Get;
use Waffler\Component\Attributes\Verbs\Post;

interface FooClient
{
    /**
     * Retrieve authorization token.
     *
     * @param array $credentials Pass the login and password.
     * @return array             The json response.
     */
    #[Post('/auth/login')]
    public function login(#[Json] array $credentials): array;

    /**
     * Retrieve all posts.
     *
     * @param string $authToken The authorization token.
     * @param array $query      Some optional query string Filters.
     * @return array            The list of posts.
     */
    #[Get('/posts')]
    public function getPosts(#[Bearer] string $authToken, #[Query] array $query = []): array;
}
```

#### Step 3: Generate the implementation for your interface and use it.

Import the class `Waffler\Component\Client\Factory` and call the method `make` passing the
_fully qualified name_ of the interface we just created as first argument and an associative array of GuzzleHttp client
options as second argument, as the example below:

```php
<?php

namespace App;

use App\Clients\FooClient;
use Waffler\Component\Client\Factory;

// Get a new factory instance.
$factory = Factory::default();

// Instantiate the FooClient interface.
$fooClient = $factory->make(FooClient::class, [
    'base_uri' => '<api-base-uri>',
]);

// That's it! Now you client instance is ready.
// Let's call our API:

$credentials = $this->fooClient->login([
    'email' => 'email@test.com',
    'password' => '<secret>'
]);
$posts = $this->fooClient->getPosts($credentials['token'], ['created_at' => '2020-01-01'])
```

## Usage examples

See the [Examples folder](./examples).

## Attributes docs

See the [wiki](https://github.com/waffler-io/waffler/wiki/The-Waffler-Attributes) for more information about the Attributes.
