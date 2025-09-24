<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lead status enumeration
 */
enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case UNQUALIFIED = 'unqualified';
    case CONVERTED = 'converted';

    /**
     * Get the display name for the status
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::UNQUALIFIED => 'Unqualified',
            self::CONVERTED => 'Converted',
        };
    }

    /**
     * Get all active statuses (excluding unqualified and converted)
     */
    public static function getActiveStatuses(): array
    {
        return [
            self::NEW,
            self::CONTACTED,
            self::QUALIFIED,
        ];
    }

    /**
     * Get all inactive statuses (unqualified and converted)
     */
    public static function getInactiveStatuses(): array
    {
        return [
            self::UNQUALIFIED,
            self::CONVERTED,
        ];
    }

    /**
     * Check if this status is considered active
     */
    public function isActive(): bool
    {
        return in_array($this, self::getActiveStatuses());
    }

    /**
     * Check if this status is considered inactive
     */
    public function isInactive(): bool
    {
        return in_array($this, self::getInactiveStatuses());
    }
}
