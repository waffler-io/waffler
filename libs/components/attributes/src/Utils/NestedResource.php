<?php

/*
 * This file is part of Waffler\Component.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Utils;

/**
 * Attribute NestedResource.
 *
 * This attribute flags the method who return another client interface.
 * Example:
 * ```php
 * interface PostsApi
 * {
 *      #[Get('posts)] // Will become "/users/{userId}/posts"
 *      public function getPosts(): array;
 * }
 *
 * interface UsersApi
 * {
 *      #[Path('users/{userId}')] // Prepends this path to PostApi routes
 *      #[NestedResource]
 *      public function posts(#[PathParam] int $userId): PostsApi;
 * }
 * ```
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[\Attribute]
class NestedResource
{
}
