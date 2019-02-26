<?php

declare(strict_types=1);

namespace Supermetrics;

use Carbon\Carbon;

class Post
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $user_id;

    /**
     * Full name of post owner.
     *
     * @var string
     */
    public $user_name;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $type;

    /**
     * Post creation datetime.
     *
     * @var \Carbon\Carbon
     */
    public $created_at;

    /**
     * Make post instance of api response data.
     *
     * @param object $postData
     *
     * @return Post
     */
    public static function fromApi(object $postData) :self
    {
        $post = new self;

        $post->id = $postData->id;
        $post->user_id = $postData->from_id;
        $post->user_name = $postData->from_name;
        $post->message = $postData->message;
        $post->type = $postData->type;
        $post->created_at = Carbon::parse($postData->created_time);

        return $post;
    }

    /**
     * Calculates post message length.
     *
     * @return int
     */
    public function length() :int
    {
        return \strlen($this->message);
    }

    /**
     * Month number, then post was created.
     *
     * @return int
     */
    public function month() :int
    {
        return $this->created_at->month;
    }

    /**
     * Week number, then post was created.
     *
     * @return int
     */
    public function weekNumber() :int
    {
        return $this->created_at->week;
    }

    /**
     * Full name of the month, then post was created.
     *
     * Example: "September", "November", "October"
     *
     * @return string
     */
    public function monthName() :string
    {
        return $this->created_at->englishMonth;
    }
}
