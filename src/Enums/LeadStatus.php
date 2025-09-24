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
     * Get all active statuses (excluding converted)
     */
    public static function getActiveStatuses(): array
    {
        return [
            self::NEW,
            self::CONTACTED,
            self::QUALIFIED,
            self::UNQUALIFIED,
        ];
    }
}
