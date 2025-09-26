<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum representing the possible statuses for leads in the CRM system.
 */
enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case CONVERTED = 'converted';
    case LOST = 'lost';

    /**
     * Get all active lead statuses.
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
     * Get all inactive lead statuses.
     */
    public static function getInactiveStatuses(): array
    {
        return [
            self::CONVERTED,
            self::LOST,
        ];
    }

    /**
     * Get a human-readable label for the status.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::CONVERTED => 'Converted',
            self::LOST => 'Lost',
        };
    }
}
