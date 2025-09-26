<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomerStatus;

/**
 * Represents a customer in the CRM system.
 */
class Customer
{
    public function __construct(
        public readonly int $id,
        public string $name,
        public string $email,
        public string $phone,
        public string $company,
        public CustomerStatus $status,
        public string $notes,
        public readonly string $created_at,
        public string $updated_at,
    ) {
    }
}
