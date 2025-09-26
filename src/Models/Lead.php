<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadStatus;

/**
 * Represents a lead in the CRM system.
 */
class Lead
{
    public function __construct(
        public readonly int $id,
        public string $name,
        public string $email,
        public string $phone,
        public string $company,
        public string $source,
        public LeadStatus $status,
        public string $notes,
        public readonly string $created_at,
        public string $updated_at,
    ) {
    }
}
