<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Supermetrics\Post;
use Supermetrics\PostsCollection;

class PostsCollectionTest extends TestCase
{
    /** @test **/
    public function itGroupsAndSortsPostsByMonth()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-03-01'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-01'])));

        $monthly = $collection->monthly();

        $this->assertCount(3, $monthly);
        $this->assertCount(2, $monthly->first());
        $this->assertCount(1, $monthly->last());
        $this->assertEquals(['January', 'February', 'March'], $monthly->keys()->toArray());
    }

    /** @test **/
    public function itGroupsAndSortsPostsByWeek()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-15'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-24'])));

        $weekly = $collection->weekly();

        $this->assertCount(3, $weekly);
        $this->assertCount(2, $weekly->first());
        $this->assertCount(1, $weekly->last());
        $this->assertEquals([1, 3, 4], $weekly->keys()->toArray());
    }

    /** @test **/
    public function itGroupsPostsByUser()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['from_name' => 'Will Smith'])));
        $collection->add(Post::fromApi((object) $this->postMock(['from_name' => 'Will Smith'])));
        $collection->add(Post::fromApi((object) $this->postMock(['from_name' => 'John Do'])));
        $collection->add(Post::fromApi((object) $this->postMock(['from_name' => 'Jenifer Aniston'])));

        $byUser = $collection->byUser();

        $this->assertCount(3, $byUser);
        $this->assertCount(2, $byUser->first());
        $this->assertCount(1, $byUser->last());
        $this->assertEquals(['Will Smith', 'John Do', 'Jenifer Aniston'], $byUser->keys()->toArray());
    }

    /** @test **/
    public function itCalculatesAveragePostsLengthInCollection()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['message' => '1'])));
        $collection->add(Post::fromApi((object) $this->postMock(['message' => '12'])));
        $collection->add(Post::fromApi((object) $this->postMock(['message' => '123'])));
        $collection->add(Post::fromApi((object) $this->postMock(['message' => '1234'])));

        $this->assertEquals(2, $collection->averageMessageLength());
    }

    /** @test **/
    public function itFindsLongestPostByCharacterLength()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['message' => '1'])));
        $collection->add(Post::fromApi((object) $this->postMock(['message' => '12'])));
        $collection->add(Post::fromApi((object) $this->postMock(['message' => '123'])));

        $longestPosts = (object) $this->postMock(['message' => '1234']);
        $collection->add(Post::fromApi($longestPosts));

        $post = $collection->longestByCharacterLength();

        $this->assertEquals($longestPosts->id, $post->id);
        $this->assertEquals($longestPosts->message, $post->message);
    }
}
