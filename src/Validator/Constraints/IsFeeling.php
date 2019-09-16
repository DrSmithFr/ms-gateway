<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Enum\FeelingEnum;

/**
 * @Annotation
 */
class IsFeeling extends IsEnum
{
    /**
     * @return string|null
     */
    public function getEnumClass(): string
    {
        return FeelingEnum::class;
    }
}
