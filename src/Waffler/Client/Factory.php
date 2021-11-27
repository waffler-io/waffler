<?php

declare(strict_types = 1);

namespace Waffler\Client;


use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use ReflectionClass;
use Waffler\Client\Contracts\ClientFactory;
use Waffler\Generator\AnonymousClassGenerator;

/**
 * Class Client
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-template TInterfaceType of object
 * @template-implements ClientFactory<TInterfaceType>
 */
class Factory implements ClientFactory
{
    /**
     * Client constructor.
     *
     * @param \ReflectionClass<TInterfaceType>           $interface
     * @param \Waffler\Generator\AnonymousClassGenerator $anonymousClassGenerator
     * @param \Waffler\Client\Proxy<TInterfaceType>      $proxy
     */
    public function __construct(
        private ReflectionClass $interface,
        private AnonymousClassGenerator $anonymousClassGenerator,
        private Proxy $proxy
    ) {
        $this->validateInterface();
    }

    /**
     * Factory method to create the client implementation.
     *
     * @param class-string<TInterfaceName> $interfaceName Fully qualified name of the client interface.
     * @param array<string, mixed>         $options       An array of guzzle http client options.
     *
     * @return TInterfaceName
     * @throws \ReflectionException
     * @throws \Exception
     * @phpstan-template TInterfaceName of object
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public static function make(string $interfaceName, array $options = []): mixed
    {
        // Instantiate the client factory with all dependencies.

        $reflectedInterface = new ReflectionClass($interfaceName);

        return (new self(
            $reflectedInterface,
            new AnonymousClassGenerator(),
            new Proxy(
                $reflectedInterface,
                new MethodInvoker(
                    new MethodReader(new ParameterReader()),
                    new ResponseParser(),
                    new GuzzleClient($options),
                )
            )
        ))
            ->makeImplementation();
    }

    /**
     * @throws \Exception
     */
    public function makeImplementation(): mixed
    {
        return $this->anonymousClassGenerator
            ->instantiateFromReflection($this->interface, $this->proxy);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateInterface(): void
    {
        if (!$this->interface->isInterface()) {
            $this->throwInvalidClassStringException($this->interface->getName());
        }
    }

    private function throwInvalidClassStringException(string $interface): void
    {
        throw new InvalidArgumentException(
            "The value \"$interface\" is not a valid fully qualified interface name.", 10
        );
    }
}
