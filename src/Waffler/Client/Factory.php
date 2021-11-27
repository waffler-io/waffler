<?php

declare(strict_types = 1);

namespace Waffler\Client;


use BadMethodCallException;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use ReflectionClass;
use Waffler\Client\Contracts\ClientFactory;
use Waffler\Generator\AnonymousClassGenerator;
use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class Client
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-template TInterfaceType of object
 * @template-implements ClientFactory<TInterfaceType>
 */
class Factory implements MethodCallHandler, ClientFactory
{
    /**
     * Client constructor.
     *
     * @param \ReflectionClass<TInterfaceType>           $interface
     * @param \Waffler\Generator\AnonymousClassGenerator $anonymousClassGenerator
     * @param \Waffler\Client\MethodInvoker              $methodInvoker
     */
    public function __construct(
        private ReflectionClass $interface,
        private AnonymousClassGenerator $anonymousClassGenerator,
        private MethodInvoker $methodInvoker
    ) {
        $this->validateInterfaceName();
    }

    /**
     * Here's where the generated client calls are
     * handled and dispatched to guzzle's client.
     *
     * @param string                   $name
     * @param array<int|string, mixed> $arguments
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!$this->interface->hasMethod($name)) {
            throw new BadMethodCallException("The method $name is not declared in {$this->interface->getName()}.");
        }

        return $this->methodInvoker->invoke($this->interface->getMethod($name), $arguments);
    }

    /**
     * Factory method to create the client implementation.
     *
     * @param class-string<TInterfaceType> $interface Fully qualified name of the client interface.
     * @param array<string, mixed>         $options An array of guzzle http client options.
     *
     * @return TInterfaceType
     * @throws \ReflectionException
     * @throws \Exception
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public static function make(string $interface, array $options = []): object
    {
        // Instantiate the client factory with all dependencies.

        return (new self(
            new ReflectionClass($interface),
            new AnonymousClassGenerator(),
            new MethodInvoker(
                new ParameterReader(),
                new ResponseParser(),
                new GuzzleClient($options),
            )
        ))
            ->makeImplementation();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateInterfaceName(): void
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

    /**
     * @throws \Exception
     */
    public function makeImplementation(): mixed
    {
        return $this->anonymousClassGenerator
            ->instantiateFromReflection($this->interface, $this);
    }
}
