<?php

declare(strict_types=1);

namespace Supermetrics\Api\Exceptions;

class BadResponse extends \Exception
{
    public function __construct(?string $message = null, int $code = 400)
    {
        $message = $message ?? 'Bad response data';

        return parent::__construct($message, $code);
    }
}
