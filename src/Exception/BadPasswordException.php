<?php

declare(strict_types = 1);

namespace App\Exception;

use Exception;

class BadPasswordException extends Exception
{
    /**
     * @var string|null
     */
    protected $message = 'bad password';

    /**
     * @var int|null
     */
    protected $code = 2;
}
