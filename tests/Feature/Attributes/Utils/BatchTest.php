<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Utils;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Verbs\Post;
use Waffler\Waffler\Tests\Fixtures\CleanStart;

/**
 * Class BatchTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class BatchTest extends TestCase
{
    use CleanStart;

    public function testItMustBatchTheCreateUserMethod(): void
    {
        $client = $this->factory->make(BatchInterface::class, [
            'handler' => $handler = new MockHandler(),
        ]);
        $handler->append(function () {
            return new Response(200, [], '1');
        });
        $handler->append(function () {
            return new Response(200, [], '2');
        });
        $handler->append(function () {
            return new Response(200, [], '3');
        });
        $responses = $client->createManyUsers([
            ['foo'],
            ['bar'],
            ['baz'],
        ]);
        $this->assertEquals(['1', '2', '3'], $responses);
        ;
    }

    public function testAllParametersMustBeArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->factory->make(BatchInterface::class);
        $client->createManyUsers([
            'foo',
            'bar',
        ]);
    }
}

interface BatchInterface
{
    #[Post('users')]
    public function createUser(#[JsonParam('name')] string $name): string;

    #[Batch('createUser')]
    public function createManyUsers(array $data): array;
}
