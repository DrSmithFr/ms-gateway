<?php

declare(strict_types = 1);

namespace App\Exception;

use Exception;

class UserNotFoundException extends Exception
{
    /**
     * @var string|null
     */
    protected $message = 'user not found';

    /**
     * @var int|null
     */
    protected $code = 404;
}
