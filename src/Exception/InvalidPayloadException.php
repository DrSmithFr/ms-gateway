<?php

declare(strict_types = 1);

namespace App\Exception;

use Exception;

class InvalidPayloadException extends Exception
{
    /**
     * @var string|null
     */
    protected $message = 'invalid payload';

    /**
     * @var int|null
     */
    protected $code = 1;
}
