<?php

namespace App\Validator;

use App\Enum\Enum;
use App\Validator\Constraints\IsEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class EnumValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param Constraint $constraint The constraint for the validation
     * @param mixed      $value The value that should be validated
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsEnum) {
            throw new UnexpectedTypeException($constraint, IsEnum::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!class_exists($constraint->getEnumClass())) {
            throw new ConstraintDefinitionException(sprintf('class %s not found', $constraint->getEnumClass()));
        }

        if (is_subclass_of($constraint->getEnumClass(), Enum::class, true)) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'class %s is not a subset of %s',
                    $constraint->getEnumClass(),
                    Enum::class
                )
            );
        }

        if (!call_user_func([$constraint->getEnumClass(), 'isValidValue'], [$value])) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setParameter('', $value)
                ->setParameters(
                    [
                        '{{string}}'   => $value,
                        '{{enum_class}}' => $constraint->getEnumClass(),
                    ]
                )
                ->addViolation();
        }
    }
}
