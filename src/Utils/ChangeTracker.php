<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Utility class for tracking changes between original and updated entity data
 */
class ChangeTracker
{
    private array $changes = [];

    /**
     * Track a field change if the values are different
     */
    public function trackField(string $fieldName, mixed $oldValue, mixed $newValue): self
    {
        $oldValue = (string) $oldValue;
        $newValue = (string) $newValue;

        if ($oldValue !== $newValue) {
            // Special handling for sensitive fields or long text
            if (strtolower($fieldName) === 'notes') {
                $this->changes[] = "{$fieldName}: Updated";
            } else {
                $this->changes[] = "{$fieldName}: '{$oldValue}' → '{$newValue}'";
            }
        }

        return $this;
    }

    /**
     * Check if any changes were tracked
     */
    public function hasChanges(): bool
    {
        return !empty($this->changes);
    }

    /**
     * Get all tracked changes as an array
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * Get changes formatted as a description string
     */
    public function getChangeDescription(string $entityType = 'Record'): string
    {
        if (empty($this->changes)) {
            return '';
        }

        return "{$entityType} information was updated:\n" . implode("\n", $this->changes);
    }

    /**
     * Reset the tracker for reuse
     */
    public function reset(): self
    {
        $this->changes = [];
        return $this;
    }

    /**
     * Static method for quick change tracking
     */
    public static function track(array $fieldMappings): self
    {
        $tracker = new self();

        foreach ($fieldMappings as $fieldName => $values) {
            [$oldValue, $newValue] = $values;
            $tracker->trackField($fieldName, $oldValue, $newValue);
        }

        return $tracker;
    }
}
