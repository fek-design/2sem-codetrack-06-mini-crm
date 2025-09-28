<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum representing the possible sources for leads in the CRM system.
 */
enum LeadSource: string
{
    case WEBSITE = 'website';
    case REFERRAL = 'referral';
    case SOCIAL_MEDIA = 'social_media';
    case EMAIL_CAMPAIGN = 'email_campaign';
    case COLD_CALL = 'cold_call';
    case TRADE_SHOW = 'trade_show';
    case OTHER = 'other';
    case NONE = 'none'; // fallback/default

    public function getDisplayName(): string
    {
        return match ($this) {
            self::WEBSITE => 'Website',
            self::REFERRAL => 'Referral',
            self::SOCIAL_MEDIA => 'Social Media',
            self::EMAIL_CAMPAIGN => 'Email Campaign',
            self::COLD_CALL => 'Cold Call',
            self::TRADE_SHOW => 'Trade Show',
            self::OTHER => 'Other',
            self::NONE => 'None',
        };
    }
}

