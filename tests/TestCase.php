<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Mocked posts response.
     *
     * @param int $page
     * @param int $per_page
     *
     * @return string
     */
    protected function postsPageResponse(int $page = 1, int $per_page = 1) :string
    {
        $posts = [];

        for ($i = 0; $i < $per_page; $i++) {
            \array_push($posts, $this->postMock());
        }

        return \json_encode([
            'data' => [
                'page' => $page,
                'posts' => $posts,
            ],
        ]);
    }

    protected function postMock(array $attributes = [])
    {
        $faker = \Faker\Factory::create();

        return \array_merge([
            'id' => $faker->uuid,
            'from_name' => $faker->name,
            'from_id' => 'user_'.$faker->numberBetween(1, 10),
            'message' => $faker->paragraph,
            'type' => 'status',
            'created_time' => $faker->iso8601, //"2019-02-21T11:24:01+00:00"
        ], $attributes);
    }
}
