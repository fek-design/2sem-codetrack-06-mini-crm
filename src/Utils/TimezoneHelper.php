<?php

declare(strict_types=1);

namespace App\Utils;

use DateTime;
use DateTimeZone;

/**
 * Utility class for handling timezone conversions between UTC and local timezone
 */
class TimezoneHelper
{
    private const APP_TIMEZONE = 'Europe/Copenhagen';
    private const UTC_TIMEZONE = 'UTC';

    /**
     * Convert a local datetime string to UTC for database storage
     */
    public static function toUtc(string $localDateTime): string
    {
        try {
            // If already in UTC format, return as-is
            if (self::isUtcFormat($localDateTime)) {
                return $localDateTime;
            }

            // Handle HTML5 datetime-local format (2025-09-20T12:00)
            if (strpos($localDateTime, 'T') !== false) {
                $dt = new DateTime($localDateTime, new DateTimeZone(self::APP_TIMEZONE));
            } else {
                // Handle standard format assuming local timezone
                $dt = new DateTime($localDateTime, new DateTimeZone(self::APP_TIMEZONE));
            }

            // Convert to UTC
            $dt->setTimezone(new DateTimeZone(self::UTC_TIMEZONE));
            return $dt->format('Y-m-d H:i:s');

        } catch (\Exception $e) {
            // Fallback to current UTC time
            return (new DateTime('now', new DateTimeZone(self::UTC_TIMEZONE)))->format('Y-m-d H:i:s');
        }
    }

    /**
     * Convert a UTC datetime from database to local timezone for display
     */
    public static function toLocal(string $utcDateTime): string
    {
        try {
            $dt = new DateTime($utcDateTime, new DateTimeZone(self::UTC_TIMEZONE));
            $dt->setTimezone(new DateTimeZone(self::APP_TIMEZONE));
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $utcDateTime; // Return original if conversion fails
        }
    }

    /**
     * Get current UTC datetime for database storage
     */
    public static function nowUtc(): string
    {
        return (new DateTime('now', new DateTimeZone(self::UTC_TIMEZONE)))->format('Y-m-d H:i:s');
    }

    /**
     * Get current local datetime for display
     */
    public static function nowLocal(): string
    {
        return (new DateTime('now', new DateTimeZone(self::APP_TIMEZONE)))->format('Y-m-d H:i:s');
    }

    /**
     * Format datetime for HTML datetime-local input (in local timezone)
     */
    public static function formatForInput(string $utcDateTime): string
    {
        try {
            $dt = new DateTime($utcDateTime, new DateTimeZone(self::UTC_TIMEZONE));
            $dt->setTimezone(new DateTimeZone(self::APP_TIMEZONE));
            return $dt->format('Y-m-d\TH:i');
        } catch (\Exception $e) {
            return (new DateTime('now', new DateTimeZone(self::APP_TIMEZONE)))->format('Y-m-d\TH:i');
        }
    }

    /**
     * Format datetime for display (in local timezone)
     */
    public static function formatForDisplay(string $utcDateTime, string $format = 'M j, Y g:i A'): string
    {
        try {
            $dt = new DateTime($utcDateTime, new DateTimeZone(self::UTC_TIMEZONE));
            $dt->setTimezone(new DateTimeZone(self::APP_TIMEZONE));
            return $dt->format($format);
        } catch (\Exception $e) {
            return $utcDateTime;
        }
    }

    /**
     * Check if datetime string appears to be in UTC format
     */
    private static function isUtcFormat(string $datetime): bool
    {
        // Simple check for standard UTC format
        return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datetime) === 1;
    }

    /**
     * Get the app timezone
     */
    public static function getAppTimezone(): string
    {
        return self::APP_TIMEZONE;
    }
}
