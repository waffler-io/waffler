<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

require __DIR__ . '/../vendor/autoload.php';

use Waffler\Component\Attributes\Request\Path;
use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Attributes\Request\Query;
use Waffler\Component\Attributes\Utils\NestedResource;
use Waffler\Component\Attributes\Verbs\Get;
use Waffler\Component\Client\Factory;

interface UsersClient
{
    #[Get('users')]
    public function get(#[Query] array $filters = []): array;

    #[Get('users/{id}')]
    public function getById(#[PathParam] int $id): array;

    // User has posts
    #[Path('users/{id}')]
    #[NestedResource]
    public function posts(#[PathParam('id')] int $userId): PostsClient;

    // User has comments
    #[Path('users/{id}')]
    #[NestedResource]
    public function comments(#[PathParam('id')] int $userId): CommentsClient;
}

interface PostsClient
{
    #[Get('posts')]
    public function get(#[Query] array $filters = []): array;

    // Post has comments
    #[Path('posts/{id}')]
    #[NestedResource]
    public function comments(#[PathParam('id')] int $postId): CommentsClient;

    // Post belongs to user
    #[Path('posts/{id}')]
    #[NestedResource]
    public function user(#[PathParam('id')] int $postId): UsersClient;
}

interface CommentsClient
{
    #[Get('comments')]
    public function get(#[Query] array $filters = []): array;

    // Comment belongs to user
    #[Path('comments/{id}')]
    #[NestedResource]
    public function user(#[PathParam('id')] int $commentId): UsersClient;

    // Comment belongs to post
    #[Path('comments/{id}')]
    #[NestedResource]
    public function post(#[PathParam('id')] int $commentId): PostsClient;
}

// Guzzle HTTP Client options
$options = ['base_uri' => 'https://jsonplaceholder.typicode.com/'];

// Instantiate UsersClient.
$usersClient = Factory::default()
    ->make(UsersClient::class, $options);

// Call normal methods.
$user = $usersClient->getById(1); // GET /users/1

// Get nested resource "posts".
$userFirstPost = $usersClient->posts(1)->get(['id' => 1]); // GET /users/1/posts?id=1

// Print the retrieved data.
print_r([
    'user' => $user['name'],
    'user_first_post' => $userFirstPost[0]['title']
]);

// Instantiate PostsClient as an independent client.
$postsClient = Factory::default()
    ->make(PostsClient::class, $options);

// Retrieve data.
$allPosts = $postsClient->get(); // GET /posts
$allCommentsOfPost = $postsClient->comments(1)->get(); // GET /posts/1/comments

// Print the retrieved data.
print_r([
    'all_users_posts' => array_map(fn ($post) => $post['title'], $allPosts),
    'all_user_post_comments' => array_map(fn ($post) => $post['body'], $allCommentsOfPost),
]);

// The possibilities are limitless.
//$usersClient->posts(1)
//    ->comments(2)
//    ->user(3)
//    ->posts(4)
//    ->get();
// Produces: /users/1/posts/2/comments/3/users/4/posts
