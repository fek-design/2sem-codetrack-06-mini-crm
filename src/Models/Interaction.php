<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents an interaction/contact history record in the CRM system.
 */
class Interaction
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $customer_id,
        public readonly ?int $lead_id,
        public string $type,
        public string $subject,
        public string $description,
        public string $interaction_date,
        public readonly string $created_at,
    ) {
    }
}
