<?php

declare(strict_types=1);

use Supermetrics\Api\Client;
use Supermetrics\Reports\PostsCount;
use Supermetrics\Reports\LongestPosts;
use Supermetrics\Reports\AveragePostsLength;
use Supermetrics\Reports\AveragePostsPerUser;
use function GuzzleHttp\json_encode;

require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$dotenv->required(['API_HOST', 'API_CLIENT_ID', 'API_EMAIL', 'API_USER_NAME']);

$posts = (new Client())->posts();


header('Content-Type: application/json');

echo json_encode([
    'data' => [
        'average_character_length' => [
            'per_post' => (new AveragePostsLength($posts))->perPost(),
            'monthly' => (new AveragePostsLength($posts))->monthly(),
        ],
        'longest_post' => [
            'monthly' => (new LongestPosts($posts))->monthly(),
        ],
        'posts_count' => [
            'weekly' => (new PostsCount($posts))->weekly(),
        ],
        'average_posts_per_user' => [
            'monthly' => (new AveragePostsPerUser($posts))->monthly(),
        ],
    ]
]);
