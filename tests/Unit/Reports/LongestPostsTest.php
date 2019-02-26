<?php

declare(strict_types=1);

namespace Tests\Unit\Reports;

use Tests\TestCase;
use Supermetrics\Post;
use Supermetrics\PostsCollection;
use Supermetrics\Reports\LongestPosts;

class LongestPostsTest extends TestCase
{
    /** @test **/
    public function itCalculatesMonthlyAveragePostsLengthReport()
    {
        $collection = new PostsCollection();

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-01-01', 'message' => '12'])));
        $longestPostJanuary = Post::fromApi((object) $this->postMock(['created_time' => '2019-01-02', 'message' => '1234']));
        $collection->add($longestPostJanuary);

        $collection->add(Post::fromApi((object) $this->postMock(['created_time' => '2019-02-02', 'message' => '1234'])));
        $longestPostFebruary = Post::fromApi((object) $this->postMock(['created_time' => '2019-02-02', 'message' => '12345']));
        $collection->add($longestPostFebruary);

        $report = (new LongestPosts($collection))->monthly();

        $this->assertCount(2, $report);
        $this->assertEquals($longestPostJanuary->id, $report->get('January')->id);
        $this->assertEquals($longestPostFebruary->id, $report->get('February')->id);
    }
}
