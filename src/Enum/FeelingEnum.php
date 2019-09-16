<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Class OverviewFeelingEnum
 */
final class FeelingEnum extends Enum
{
    /**
     * Distraction feelings
     */
    public const SPEED       = 'speed';
    public const DISCTRACTED = 'distracted';
    public const AGITATED    = 'agitated';

    /**
     * Positive feelings
     */
    public const ACTIVE    = 'active';
    public const CONFIDENT = 'confident';
    public const RELAXED   = 'relaxed';

    /**
     * Weariness feelings
     */
    public const TIRED  = 'tired';
    public const SLOW   = 'slow';
    public const GUILTY = 'guilty';

    /**
     * Anxiety feelings
     */
    public const ANXIOUS = 'anxious';
    public const AFRAID  = 'afraid';
    public const ANGRY   = 'angry';
}
