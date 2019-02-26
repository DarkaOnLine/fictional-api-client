<?php

declare(strict_types=1);

namespace Tests\Unit\Reports;

use Tests\TestCase;
use Supermetrics\Post;
use Supermetrics\PostsCollection;
use Supermetrics\Reports\AveragePostsPerUser;

class AveragePostsPerUserTest extends TestCase
{
    /** @test **/
    public function itCalculatesMonthlyAveragePostsPerUser()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01', 'from_id' => '1'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02', 'from_id' => '2'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02', 'from_id' => '2'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02', 'from_id' => '2'])));

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-02', 'from_id' => '3'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-02', 'from_id' => '3'])));
        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-02', 'from_id' => '3'])));

        $report = (new AveragePostsPerUser($collection))->monthly();

        $this->assertCount(2, $report);
        $this->assertEquals(['January' => 2, 'February' => 3], $report->toArray());
    }
}
