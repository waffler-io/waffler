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
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $returnType
     * @param string|null                         $wrapperProperty
     *
     * @return mixed
     */
    public function parse(ResponseInterface $response, string $returnType, ?string $wrapperProperty = null): mixed
    {
        return match ($returnType) {
            'array' => $this->decode($response, $wrapperProperty),
            'void', 'null' => null,
            'bool' => true,
            'string' => $response->getBody()->getContents(),
            'int', 'float', 'double' => $response->getStatusCode(),
            'object', ArrayObject::class => new ArrayObject(
                $this->decode($response, $wrapperProperty),
                ArrayObject::ARRAY_AS_PROPS
            ),
            StreamInterface::class => $response->getBody(),
            ResponseInterface::class, Response::class, MessageInterface::class, 'mixed' => $response,
            default => throw new TypeError()
        };
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param null|string $wrapperProperty
     *
     * @return array<int|string, mixed>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function decode(ResponseInterface $response, ?string $wrapperProperty): array
    {
        $response = (array)json_decode($response->getBody()->getContents(), true);
        if ($wrapperProperty) {
            return array_get($response, $wrapperProperty);
        }
        return $response;
    }
}
