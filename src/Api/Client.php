<?php

declare(strict_types=1);

namespace Supermetrics\Api;

use Supermetrics\Post;
use Supermetrics\PostsCollection;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Supermetrics\Api\Exceptions\BadResponse;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Supermetrics\Api\Exceptions\AuthentificationFailed;

class Client
{
    /**
     * Cash key for storing authentification token.
     */
    const TOKEN_CACHE_KEY = 'supermetrics.api.token';

    public $posts = [];

    /**
     * @var \GuzzleHttp\Client
     */
    protected $api;

    /**
     * @var \Symfony\Component\Cache\Simple\AbstractCache
     */
    protected $cache;

    public function __construct(array $options = [], ?\Psr\SimpleCache\CacheInterface $cacheAdapter = null)
    {
        $options = $options + [
            'base_uri' => \getenv('API_HOST'),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $this->api = new \GuzzleHttp\Client($options);

        $this->cache = $cacheAdapter ?? new FilesystemCache();

        $this->authenticate();
    }

    /**
     * Fetch all posts.
     *
     * @param int $page page number to start from
     * @param array $posts
     *
     * @return PostsCollection
     */
    public function posts(int $page = 1, array &$posts = []) :PostsCollection
    {
        $this->api->requestAsync(
            'GET',
            'assignment/posts',
            ['query' => ['sl_token' => $this->token(), 'page' => $page]]
        )->then(
            function (ResponseInterface $res) use ($page, &$posts) {
                if ($res->getStatusCode() !== 200) {
                    throw new BadResponse('Cannot get posts from one of pages', $res->getStatusCode());
                }

                try {
                    $response = \json_decode($res->getBody()->getContents());

                    if ($response->data->page > ($page - 1)) {
                        $posts = \array_merge($posts, $response->data->posts);

                        $this->posts($page + 1, $posts);
                    }
                } catch (\Exception $e) {
                    throw new BadResponse($e->getMessage());
                }
            },
            function (RequestException $e) {
                throw $e;
            }
        )->wait();

        $collection = new PostsCollection();

        collect($posts)->each(function ($post) use ($collection) {
            $collection->add(Post::fromApi($post));
        });

        return $collection;
    }

    /**
     * Authenticate and set authentification token to cache.
     *
     * @return string token
     */
    protected function authenticate() :string
    {
        if (! $this->cache->has(static::TOKEN_CACHE_KEY)) {
            $response = $this->api->post('assignment/register', [
                'form_params' => [
                    'client_id' => \getenv('API_CLIENT_ID'),
                    'email' => \getenv('API_EMAIL'),
                    'name' => \getenv('API_USER_NAME'),
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new AuthentificationFailed();
            }

            try {
                $token = \json_decode($response->getBody()->getContents())->data->sl_token;
            } catch (\Exception $e) {
                throw new BadResponse($e->getMessage());
            }

            /*
            Lets cache the token for future use.
            Cache expires in 55min.
            Max ttl for token is 60min.
            */
            $this->cache->set(static::TOKEN_CACHE_KEY, $token, 3300);

            return $token;
        }

        return $this->cache->get(static::TOKEN_CACHE_KEY);
    }

    /**
     * Get authentification token.
     *
     * @return string
     */
    protected function token() :string
    {
        return $this->authenticate();
    }
}
