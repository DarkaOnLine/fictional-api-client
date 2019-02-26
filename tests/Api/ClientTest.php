<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Supermetrics\Api\Client as ApiClient;
use Supermetrics\Api\Exceptions\BadResponse;
use Symfony\Component\Cache\Simple\ArrayCache;
use Supermetrics\Api\Exceptions\AuthentificationFailed;

class ClientTest extends TestCase
{
    /** @test */
    public function itStoresAuthentificationTokenInCache()
    {
        $fakeToken = 'token123';

        $handlerMock = new MockHandler([
            new Response(200, [], \json_encode(['data' => ['sl_token' => $fakeToken]])),
        ]);

        $cache = new ArrayCache();

        // Before cache
        $this->assertFalse($cache->has(ApiClient::TOKEN_CACHE_KEY));

        new ApiClient(['handler' => $handlerMock], $cache);

        // Cache should have api token stored and returned it form cache.
        $this->assertTrue($cache->has(ApiClient::TOKEN_CACHE_KEY));
        $this->assertEquals($fakeToken, $cache->get(ApiClient::TOKEN_CACHE_KEY));
        $cache->delete(ApiClient::TOKEN_CACHE_KEY);
    }

    /** @test */
    public function itThrowsExceptionIfResponseCodeIsNot200()
    {
        $this->expectException(AuthentificationFailed::class);

        $handlerMock = new MockHandler([
            new Response(500),
        ]);

        new ApiClient(['handler' => $handlerMock], new \Symfony\Component\Cache\Simple\NullCache());
    }

    /** @test */
    public function itThrowsExceptionIfAuthenticationResponseDataIsInValid()
    {
        $this->expectException(BadResponse::class);

        $handlerMock = new MockHandler([
            new Response(200, [], \json_encode(['data' => ['error' => '']])),
        ]);

        new ApiClient(['handler' => $handlerMock], new \Symfony\Component\Cache\Simple\NullCache());
    }

    /** @test */
    public function itGetsPaginatedPosts()
    {
        $handlerMock = new MockHandler([
            new Response(200, [], $this->postsPageResponse(1)),
            new Response(200, [], $this->postsPageResponse(2)),
            new Response(200, [], $this->postsPageResponse(3)),
            new Response(200, [], $this->postsPageResponse(3)),
        ]);

        $api = new ApiClient(['handler' => $handlerMock], $this->cache());

        $this->assertCount(3, $api->posts());
    }

    /** @test */
    public function itThrowsExceptionIfOneOfTheFetchedPagesFails()
    {
        $this->expectException(BadResponse::class);

        $handlerMock = new MockHandler([
            new Response(200, [], $this->postsPageResponse(1)),
            new Response(500, [], $this->postsPageResponse(2)),
        ]);

        $api = new ApiClient(['handler' => $handlerMock], $this->cache());

        $api->posts();
    }

    /** @test */
    public function itThrowsExceptionPostsResponseDataIsIncorrect()
    {
        $this->expectException(BadResponse::class);

        $handlerMock = new MockHandler([
            new Response(200, [], $this->postsPageResponse(1)),
            new Response(200, [], \json_encode(['data' => ['some' => 'data']])),
        ]);

        $api = new ApiClient(['handler' => $handlerMock], $this->cache());

        $api->posts();
    }

    /**
     * Cache with cached api token.
     *
     * @return \Psr\SimpleCache\CacheInterface
     */
    protected function cache() :\Psr\SimpleCache\CacheInterface
    {
        $cache = new ArrayCache();
        $cache->set(ApiClient::TOKEN_CACHE_KEY, 'token', 3300);

        return $cache;
    }
}
