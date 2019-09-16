<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Enum\EventEnum;
use App\Validator\EnumValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
abstract class IsEnum extends Constraint
{
    /**
     * @var string|null
     */
    public $message = 'The string "{{ string }}" is not a valid value of {{enum_class}}';

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return EnumValidator::class;
    }

    /**
     * @return string|null
     */
    abstract public function getEnumClass(): string;
}
