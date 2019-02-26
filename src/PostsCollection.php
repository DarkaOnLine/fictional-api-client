<?php

declare(strict_types=1);

namespace Supermetrics;

use Tightenco\Collect\Support\Collection;

class PostsCollection extends Collection
{
    /**
     * Add Post onto the end of the collection.
     *
     * @param Post $value
     * @param Post $post
     *
     * @return PostsCollection
     */
    public function add(Post $post) :self
    {
        $this->push($post);

        return $this;
    }

    /**
     * Group posts by month.
     *
     * @return PostsCollection
     */
    public function monthly() :self
    {
        return $this->sortBy(function ($post) {
            return $post->month();
        })->groupBy(function ($post) {
            return $post->monthName();
        });
    }

    /**
     * Group posts by week.
     *
     * @return PostsCollection
     */
    public function weekly() :self
    {
        return $this->sortBy(function ($post) {
            return $post->weekNumber();
        })->groupBy(function ($post) {
            return $post->weekNumber();
        });
    }

    /**
     * Group posts by user full name.
     *
     * @return PostsCollection
     */
    public function byUser() :self
    {
        return $this->groupBy(function ($post) {
            return $post->user_name;
        });
    }

    /**
     * Group posts by user full name.
     *
     * @return PostsCollection
     */
    public function byUserId() :self
    {
        return $this->groupBy(function ($post) {
            return $post->user_id;
        });
    }

    /**
     * Calculate average post length in collection.
     *
     * @return int
     */
    public function averageMessageLength() :int
    {
        return (int) \round($this->average(function ($post) {
            return $post->length();
        }), 0, PHP_ROUND_HALF_DOWN);
    }

    /**
     * Find longest post in collection by character length.
     *
     * @return Post
     */
    public function longestByCharacterLength() :Post
    {
        return $this->reduce(function ($carry, $post) {
            if (! $carry) {
                return $post;
            }

            if ($carry->length() > $post->length()) {
                return $carry;
            }

            return $post;
        });
    }
}
