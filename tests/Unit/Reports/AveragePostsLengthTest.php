<?php

declare(strict_types=1);

namespace Tests\Unit\Reports;

use Tests\TestCase;
use Supermetrics\Post;
use Supermetrics\PostsCollection;
use Supermetrics\Reports\AveragePostsLength;

class AveragePostsLengthTest extends TestCase
{
    /** @test **/
    public function itCalculatesMonthlyAveragePostsLength()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01', 'message' => '12'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02', 'message' => '1234'])));

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-02', 'message' => '1234'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-03', 'message' => '123456'])));

        $report = (new AveragePostsLength($collection))->monthly();

        $this->assertCount(2, $report);
        $this->assertEquals(['January' => 3, 'February' => 5], $report->toArray());
    }
}
