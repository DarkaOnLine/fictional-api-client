<?php

declare(strict_types=1);

namespace Supermetrics\Reports;

use Supermetrics\PostsCollection;

class AveragePostsLength
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
        return $this->collection->monthly()->map(function ($posts) {
            return $posts->averageMessageLength();
        });
    }

    public function perPost() :int
    {
        return (int) \round($this->collection->averageMessageLength(), 0, PHP_ROUND_HALF_DOWN);
    }
}
