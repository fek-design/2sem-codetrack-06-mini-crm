<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents an interaction/contact history record in the CRM system.
 */
class Interaction
{
    public function __construct(
        private readonly int $id,
        private readonly ?int $customer_id,
        private readonly ?int $lead_id,
        private string $type,
        private string $subject,
        private string $description,
        private string $interaction_date,
        private readonly string $created_at,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    public function getLeadId(): ?int
    {
        return $this->lead_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getInteractionDate(): string
    {
        return $this->interaction_date;
    }

    public function setInteractionDate(string $interaction_date): void
    {
        $this->interaction_date = $interaction_date;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
}
