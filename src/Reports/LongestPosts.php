<?php

declare(strict_types=1);

namespace Supermetrics\Reports;

use Supermetrics\PostsCollection;

class LongestPosts
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
            return $posts->longestByCharacterLength();
        });
    }
}
