<?php

declare(strict_types=1);

namespace Supermetrics\Reports;

use Supermetrics\PostsCollection;

class AveragePostsPerUser
{
    /**
     * Posts collection.
     *
     * @var \Supermetrics\PostsCollection
     */
    protected $collection;

    public function __construct(PostsCollection $collection)
    {
        $this->collection = $collection;
    }

    public function monthly() :\Tightenco\Collect\Support\Collection
    {
        return $this->collection->monthly()->map(function ($monthPosts) {
            return \round($monthPosts->count() / $monthPosts->byUserId()->count(), 0, PHP_ROUND_HALF_DOWN);
        });
    }
}
