<?php

/**
 * @return array{
 *     clients: array<class-string|int, class-string|array<string, mixed>>,
 *     aliases: array<class-string, string>,
 *     global_options: array<string, mixed>,
 *     singletons: array<int, class-string>
 * }
 */
return [
    /*
     * Put here your client specifications, and it will be auto-registered in the service container.
     */
    'clients' => [
        // 'App\\Clients\\MyClient' => ['base_uri' => env('EXAMPLE_CLIENT_BASE_URI')],
        // 'App\\Clients\\ClientWithoutConfiguration',
    ],

    /*
     * Register an alias for your clients.
     */
    'aliases' => [
        // 'App\\Clients\\MyClient' => 'example-alias'
    ],

    /*
     * Shared configuration to be used in every client.
     */
    'global_options' => [
        // 'headers' => ['X-Foo-Bar' => 'Baz']
    ],

    /*
     * Clients that must be bound as singletons.
     *
     * This type of binding is recommended when you don't need to provide guzzle http options in runtime. Singleton
     * bindings are always faster to resolve from the container than regular bindings after the first instantiation.
     */
    'singletons' => [
        // 'App\\Clients\\MyClient',
    ],
];
