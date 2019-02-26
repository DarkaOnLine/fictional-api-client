<?php

declare(strict_types=1);

namespace Supermetrics\Reports;

use Supermetrics\PostsCollection;

class PostsCount
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

    public function weekly()  :\Tightenco\Collect\Support\Collection
    {
        return $this->collection->weekly()->map(function ($posts) {
            return $posts->count();
        });
    }
}
