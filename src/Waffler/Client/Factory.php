<?php

declare(strict_types = 1);

namespace Waffler\Client;


use BadMethodCallException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionException;
use Waffler\Generator\AnonymousClassGenerator;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class Client
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package  Waffler
 * @template TInterfaceType of object
 */
class Factory implements MethodCallHandler
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private ClientInterface $client;

    /**
     * @var \ReflectionClass<TInterfaceType> $interface
     */
    private ReflectionClass $interface;

    /**
     * Client constructor.
     *
     * @param class-string<TInterfaceType> $interfaceClass
     * @param array<string,mixed>          $guzzleClientConfig
     *
     * @throws \InvalidArgumentException
     */
    private function __construct(string $interfaceClass, array $guzzleClientConfig = [])
    {
        $this->validateInterfaceName($interfaceClass);
        $this->client = new GuzzleClient($guzzleClientConfig);
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

        return $this->callClientMethod($name, $arguments);
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @param class-string<TInterfaceName> $interface
     * @param array<string, mixed>         $options
     *
     * @return TInterfaceName
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TInterfaceName of object
     */
    public static function make(string $interface, array $options = []): object
    {
        return (new self($interface, $options))->generate();
    }

    /**
     * @param class-string<TInterfaceType> $interfaceClass
     *
     * @throws \InvalidArgumentException
     */
    private function validateInterfaceName(string $interfaceClass): void
    {
        try {
            $this->interface = new ReflectionClass($interfaceClass);
            if (!$this->interface->isInterface()) {
                $this->throwInvalidInterfaceException($interfaceClass);
            }
        } catch (ReflectionException) {
            $this->throwInvalidInterfaceException($interfaceClass);
        }
    }

    private function throwInvalidInterfaceException(string $interface): void
    {
        throw new InvalidArgumentException("The value \"$interface\" is not a valid fully qualified interface name.");
    }

    /**
     * @param string                   $name
     * @param array<int|string, mixed> $arguments
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callClientMethod(string $name, array $arguments): mixed
    {
        return $this->newMethod($name, $arguments)->call();
    }

    /**
     * Generates the interface implementation at runtime.
     *
     * @return TInterfaceType
     */
    private function generate(): mixed
    {
        return $this->newAnonymousClassGenerator()
            ->instantiateFromReflection(
                $this->interface,
                $this
            );
    }

    /**
     * Retrieves new implementer instance.
     *
     * @return InterfaceInstantiator<TInterfaceType>
     * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Pure]
    private function newAnonymousClassGenerator(): InterfaceInstantiator
    {
        return new AnonymousClassGenerator();
    }

    /**
     * @param string                   $name
     * @param array<int|string, mixed> $arguments
     *
     * @return \Waffler\Client\Method<TInterfaceType>
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function newMethod(string $name, array $arguments): Method
    {
        return new Method(
            $this->interface->getMethod($name),
            $arguments,
            $this->client
        );
    }
}
