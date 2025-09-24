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
     * Get all customer status values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display name for the status
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
     * Get CSS class for status badge
     */
    public function getCssClass(): string
    {
        return 'status-' . $this->value;
    }

    /**
     * Check if status is active (customer is currently engaged)
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE || $this === self::VIP || $this === self::ONBOARDING;
    }

    /**
     * Create from string value
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
