<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use RuntimeException;
use App\Validator\EnumValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class isEnum extends Constraint
{
    /**
     * @var string|null
     */
    private $enumClass;

    /**
     * @var string|null
     */
    public $message = 'The string "{{ string }}" is not a valid value of {{enum_class}}';

    /**
     * Feeling constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        parent::__construct($options);

        if (!$enumClass = $options['class']) {
            throw new RuntimeException('Enum constraint need "enum_class" to be defined', 5);
        }

        $this->enumClass = $enumClass;
    }

    /**
     * @return string|null
     */
    public function getEnumClass(): ?string
    {
        return $this->enumClass;
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return EnumValidator::class;
    }
}
