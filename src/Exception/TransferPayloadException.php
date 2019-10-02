<?php

declare(strict_types = 1);

namespace App\Exception;

use Exception;

class TransferPayloadException extends Exception
{
    /**
     * @var string|null
     */
    protected $message = 'bad payload';

    /**
     * @var int|null
     */
    protected $code = 1;
}
