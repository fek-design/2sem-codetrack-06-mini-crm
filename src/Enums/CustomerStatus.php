<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Customer status enumeration
 */
enum CustomerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case VIP = 'vip';
    case ONBOARDING = 'onboarding';

    /**
     * Get the display name for the status
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::VIP => 'VIP',
            self::ONBOARDING => 'Onboarding',
        };
    }

    /**
     * Get all active customer statuses
     */
    public static function getActiveStatuses(): array
    {
        return [
            self::ACTIVE,
            self::VIP,
            self::ONBOARDING,
        ];
    }
}
