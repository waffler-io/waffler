<?php

declare(strict_types = 1);

namespace Waffler\Client;

use ArrayObject;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TypeError;

use function Waffler\array_get;

/**
 * Class ResponseTransformer.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package  Waffler\Client
 * @internal
 */
class ResponseParser
{
    /**
     * @param \Psr\Http\Message\ResponseInterface    $response
     * @param \Waffler\Client\Method<TInterfaceType> $method
     *
     * @return mixed
     * @template TInterfaceType of object
     */
    public function parse(ResponseInterface $response, Method $method): mixed
    {
        return match ($method->getReturnType()) {
            'array' => $this->decode($response, $method),
            'void', 'null' => null,
            'bool' => true,
            'string' => $response->getBody()->getContents(),
            'int', 'float', 'double' => $response->getStatusCode(),
            'object', ArrayObject::class => new ArrayObject(
                $this->decode($response, $method),
                ArrayObject::ARRAY_AS_PROPS
            ),
            StreamInterface::class => $response->getBody(),
            ResponseInterface::class, Response::class, MessageInterface::class, 'mixed' => $response,
            default => throw new TypeError()
        };
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface    $response
     * @param \Waffler\Client\Method<TInterfaceType> $method
     *
     * @return array<int|string, mixed>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TInterfaceType of object
     */
    private function decode(ResponseInterface $response, Method $method): array
    {
        $response = (array)json_decode($response->getBody()->getContents(), true);
        if ($method->mustUnwrap() && !empty($response)) {
            return array_get($response, $method->getWrapperProperty());
        }
        return $response;
    }
}
