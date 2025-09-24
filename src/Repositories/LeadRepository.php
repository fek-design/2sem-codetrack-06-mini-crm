<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use App\Models\Lead;
use App\Utils\TimezoneHelper;
use PDO;

/**
 * Repository for managing lead data in the database.
 */
class LeadRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM leads ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);

        $leads = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leads[] = $this->mapRowToLead($row);
        }

        return $leads;
    }

    public function findById(int $id): ?Lead
    {
        $sql = "SELECT * FROM leads WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToLead($row) : null;
    }

    public function create(string $name, string $email, string $phone, string $company, string $source, string $notes = ''): Lead
    {
        $sql = "INSERT INTO leads (name, email, phone, company, source, notes, created_at, updated_at)
                VALUES (:name, :email, :phone, :company, :source, :notes, :created_at, :updated_at)";

        $now = TimezoneHelper::nowUtc();

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'source' => $source,
            'notes' => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function update(int $id, string $name, string $email, string $phone, string $company, string $source, string $status, string $notes): bool
    {
        $sql = "UPDATE leads SET name = :name, email = :email, phone = :phone,
                company = :company, source = :source, status = :status, notes = :notes, updated_at = :updated_at
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'source' => $source,
            'status' => $status,
            'notes' => $notes,
            'updated_at' => TimezoneHelper::nowUtc(),
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM leads WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function convertToCustomer(int $leadId): bool
    {
        // This will be used to convert a lead to a customer
        // The actual conversion logic will be in the controller
        return $this->update($leadId, '', '', '', '', '', 'converted', '');
    }

    public function countByStatus(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM leads GROUP BY status";
        $stmt = $this->db->query($sql);

        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $counts[$row['status']] = (int) $row['count'];
        }

        return $counts;
    }

    public function countBySource(): array
    {
        $sql = "SELECT source, COUNT(*) as count FROM leads GROUP BY source";
        $stmt = $this->db->query($sql);

        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $counts[$row['source']] = (int) $row['count'];
        }

        return $counts;
    }

    private function mapRowToLead(array $row): Lead
    {
        return new Lead(
            id: (int) $row['id'],
            name: $row['name'],
            email: $row['email'],
            phone: $row['phone'] ?? '',
            company: $row['company'] ?? '',
            source: $row['source'] ?? '',
            status: $row['status'],
            notes: $row['notes'] ?? '',
            created_at: $row['created_at'],
            updated_at: $row['updated_at'],
        );
    }
}
