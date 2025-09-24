<?php

declare(strict_types=1);

namespace App\Utils;

use App\Config;
use DateTime;
use DateTimeZone;

/**
 * Utility class for handling timezone conversions between UTC and local timezone
 */
class TimezoneHelper
{
    private const UTC_TIMEZONE = 'UTC';

    /**
     * Get the application timezone from config
     */
    private static function getAppTimezone(): string
    {
        return Config::get('APP_TIMEZONE', 'Europe/Copenhagen');
    }

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
                $dt = new DateTime($localDateTime, new DateTimeZone(self::getAppTimezone()));
            } else {
                // Handle standard format assuming local timezone
                $dt = new DateTime($localDateTime, new DateTimeZone(self::getAppTimezone()));
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
            $dt->setTimezone(new DateTimeZone(self::getAppTimezone()));
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $utcDateTime; // Return original if conversion fails
        }
    }

    /**
     * Format a datetime string for display using configured format
     */
    public static function formatForDisplay(string $utcDateTime, ?string $format = null): string
    {
        if (empty($utcDateTime)) {
            return '';
        }

        try {
            $dt = new DateTime($utcDateTime, new DateTimeZone(self::UTC_TIMEZONE));
            $dt->setTimezone(new DateTimeZone(self::getAppTimezone()));

            // Use provided format or default from config
            $displayFormat = $format ?? Config::get('DISPLAY_DATETIME_FORMAT', 'M j, Y g:i A');
            return $dt->format($displayFormat);
        } catch (\Exception $e) {
            return $utcDateTime; // Return original if formatting fails
        }
    }

    /**
     * Format a datetime string for display using date-only format
     */
    public static function formatDateForDisplay(string $utcDateTime): string
    {
        if (empty($utcDateTime)) {
            return '';
        }

        try {
            $dt = new DateTime($utcDateTime, new DateTimeZone(self::UTC_TIMEZONE));
            $dt->setTimezone(new DateTimeZone(self::getAppTimezone()));

            $displayFormat = Config::get('DISPLAY_DATE_FORMAT', 'M j, Y');
            return $dt->format($displayFormat);
        } catch (\Exception $e) {
            return $utcDateTime;
        }
    }

    /**
     * Format a datetime string for HTML input fields
     */
    public static function formatForInput(string $utcDateTime): string
    {
        if (empty($utcDateTime)) {
            return '';
        }

        try {
            $dt = new DateTime($utcDateTime, new DateTimeZone(self::UTC_TIMEZONE));
            $dt->setTimezone(new DateTimeZone(self::getAppTimezone()));
            return $dt->format('Y-m-d\TH:i'); // HTML5 datetime-local format
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get current UTC datetime
     */
    public static function nowUtc(): string
    {
        return (new DateTime('now', new DateTimeZone(self::UTC_TIMEZONE)))->format('Y-m-d H:i:s');
    }

    /**
     * Get current local datetime
     */
    public static function nowLocal(): string
    {
        return (new DateTime('now', new DateTimeZone(self::getAppTimezone())))->format('Y-m-d H:i:s');
    }

    /**
     * Check if a datetime string is already in UTC format
     */
    private static function isUtcFormat(string $datetime): bool
    {
        // Check for common UTC indicators
        return str_ends_with($datetime, 'Z') ||
               str_contains($datetime, '+00:00') ||
               str_contains($datetime, 'UTC');
    }
}
