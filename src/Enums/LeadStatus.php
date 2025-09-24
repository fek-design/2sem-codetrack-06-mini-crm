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
     * Get all lead status values
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
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::UNQUALIFIED => 'Unqualified',
            self::CONVERTED => 'Converted',
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
     * Check if lead can still be converted
     */
    public function canConvert(): bool
    {
        return $this !== self::CONVERTED && $this !== self::UNQUALIFIED;
    }

    /**
     * Check if lead is in active pipeline
     */
    public function isInPipeline(): bool
    {
        return $this === self::NEW || $this === self::CONTACTED || $this === self::QUALIFIED;
    }

    /**
     * Create from string value
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
