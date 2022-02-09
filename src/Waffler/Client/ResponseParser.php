<?php

declare(strict_types=1);

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client;

use ArrayObject;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TypeError;

use function Waffler\Waffler\arrayGet;

/**
 * Class ResponseTransformer.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package  Waffler\Waffler\Client
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
            'bool' => $response->getStatusCode() < 400,
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
     * @param null|string                         $wrapperProperty
     *
     * @return array<int|string, mixed>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function decode(ResponseInterface $response, ?string $wrapperProperty): array
    {
        $response = (array) json_decode($response->getBody()->getContents(), true);
        if ($wrapperProperty) {
            return arrayGet($response, $wrapperProperty);
        }
        return $response;
    }
}
