<?php

declare(strict_types=1);

namespace Supermetrics\Api\Exceptions;

class AuthentificationFailed extends \Exception
{
    public function __construct(?string $message = null)
    {
        $message = $message ?? 'Authentification failed';

        return parent::__construct($message, 401);
    }
}
