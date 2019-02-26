<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Supermetrics\Post;

class PostTest extends TestCase
{
    /** @test **/
    public function itMakesPostFromApiResponsePostData()
    {
        $postMock = (object) $this->postMock();

        $post = Post::fromApi($postMock);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($postMock->id, $post->id);
        $this->assertEquals($postMock->from_id, $post->user_id);
        $this->assertEquals($postMock->from_name, $post->user_name);
        $this->assertInstanceOf(\Carbon\Carbon::class, $post->created_at);
    }

    /** @test **/
    public function itCalculatesPostLength()
    {
        $message = 'supermetrics message';
        $postMock = (object) $this->postMock(['message' => $message]);
        $post = Post::fromApi($postMock);

        $this->assertEquals(20, $post->length());
    }

    /** @test **/
    public function itKnowsPostCreationMonthName()
    {
        $created_time = '2019-01-21T11:24:01+00:00';
        $postMock = (object) $this->postMock(['created_time' => $created_time]);
        $post = Post::fromApi($postMock);

        $this->assertEquals('January', $post->monthName());
    }

    /** @test **/
    public function itKnowsPostCreationWeekNumber()
    {
        $firstWeekPost = Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01']));
        $thirdWeekPost = Post::fromApi((object) $this->postMock(['created_time' => '2019-01-15']));

        $this->assertEquals(1, $firstWeekPost->weekNumber());
        $this->assertEquals(3, $thirdWeekPost->weekNumber());
    }
}
