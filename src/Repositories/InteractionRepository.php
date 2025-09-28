<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use App\Models\Interaction;
use App\Utils\TimezoneHelper;
use PDO;

/**
 * Repository for managing interaction/contact history data in the database.
 */
class InteractionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByCustomerId(int $customerId): array
    {
        $sql = "SELECT * FROM interactions WHERE customer_id = :customer_id ORDER BY interaction_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['customer_id' => $customerId]);

        $interactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $interactions[] = $this->mapRowToInteraction($row);
        }

        return $interactions;
    }

    public function findByLeadId(int $leadId): array
    {
        $sql = "SELECT * FROM interactions WHERE lead_id = :lead_id ORDER BY interaction_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['lead_id' => $leadId]);

        $interactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $interactions[] = $this->mapRowToInteraction($row);
        }

        return $interactions;
    }

    public function createForCustomer(int $customerId, string $type, string $subject, string $description, ?string $interactionDate = null): Interaction
    {
        $sql = "INSERT INTO interactions (customer_id, type, subject, description, interaction_date, created_at)
                VALUES (:customer_id, :type, :subject, :description, :interaction_date, :created_at)";

        $now = TimezoneHelper::nowUtc();

        // Convert interaction date to UTC for storage
        if ($interactionDate) {
            $interactionDate = TimezoneHelper::toUtc($interactionDate);
        } else {
            $interactionDate = $now;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'customer_id' => $customerId,
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'interaction_date' => $interactionDate,
            'created_at' => $now,
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function createForLead(int $leadId, string $type, string $subject, string $description, ?string $interactionDate = null): Interaction
    {
        $sql = "INSERT INTO interactions (lead_id, type, subject, description, interaction_date, created_at)
                VALUES (:lead_id, :type, :subject, :description, :interaction_date, :created_at)";

        $now = TimezoneHelper::nowUtc();

        // Convert interaction date to UTC for storage
        if ($interactionDate) {
            $interactionDate = TimezoneHelper::toUtc($interactionDate);
        } else {
            $interactionDate = $now;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'lead_id' => $leadId,
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'interaction_date' => $interactionDate,
            'created_at' => $now,
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function findById(int $id): ?Interaction
    {
        $sql = "SELECT * FROM interactions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToInteraction($row) : null;
    }

    public function getRecentInteractions(int $limit = 10): array
    {
        $sql = "SELECT * FROM interactions ORDER BY interaction_date DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['limit' => $limit]);

        $interactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $interactions[] = $this->mapRowToInteraction($row);
        }

        return $interactions;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM interactions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function mapRowToInteraction(array $row): Interaction
    {
        return new Interaction(
            id: (int) $row['id'],
            customer_id: $row['customer_id'] ? (int) $row['customer_id'] : null,
            lead_id: $row['lead_id'] ? (int) $row['lead_id'] : null,
            type: $row['type'],
            subject: $row['subject'],
            description: $row['description'],
            interaction_date: $row['interaction_date'],
            created_at: $row['created_at'],
        );
    }

    /**
     * Normalize datetime input to UTC format for storage
     * @deprecated Use TimezoneHelper::toUtc() instead
     */
    private function normalizeDateTime(string $datetime): string
    {
        return TimezoneHelper::toUtc($datetime);
    }
}
