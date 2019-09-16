<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Enum\EventEnum;

/**
 * @Annotation
 */
class IsEvent extends IsEnum
{
    /**
     * @return string|null
     */
    public function getEnumClass(): string
    {
        return EventEnum::class;
    }
}
