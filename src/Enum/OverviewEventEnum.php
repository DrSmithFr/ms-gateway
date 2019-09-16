<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Class OverviewEventEnum
 */
final class OverviewEventEnum extends Enum
{
    public const SOCIALIZATION = 'social';
    public const ACHIEVEMENT   = 'achievement';
    public const RELAXATION    = 'relax';
    public const CONFLICT      = 'conflict';
    public const PANIC_ATTACK  = 'panic';
    public const BOREDOM       = 'boredom';
}
