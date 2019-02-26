<?php

declare(strict_types=1);

namespace Tests\Unit\Reports;

use Tests\TestCase;
use Supermetrics\Post;
use Supermetrics\PostsCollection;
use Supermetrics\Reports\PostsCount;

class WeeklyPostsCountTest extends TestCase
{
    /** @test **/
    public function itCalculatesWeeklyPostsCountReport()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-15'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-24'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-03-22'])));

        $report = (new PostsCount($collection))->weekly();

        $this->assertCount(4, $report);
        $this->assertEquals([1 => 2, 3 => 1, 4 => 1, 12 => 1], $report->toArray());
    }
}
