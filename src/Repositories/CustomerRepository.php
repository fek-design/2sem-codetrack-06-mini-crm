<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use App\Models\Customer;
use App\Utils\TimezoneHelper;
use PDO;

/**
 * Repository for managing customer data in the database.
 */
class CustomerRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM customers ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);

        $customers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = $this->mapRowToCustomer($row);
        }

        return $customers;
    }

    public function findById(int $id): ?Customer
    {
        $sql = "SELECT * FROM customers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToCustomer($row) : null;
    }

    public function findByEmail(string $email): ?Customer
    {
        $sql = "SELECT * FROM customers WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToCustomer($row) : null;
    }

    public function create(string $name, string $email, string $phone, string $company, string $notes = ''): Customer
    {
        $sql = "INSERT INTO customers (name, email, phone, company, notes, created_at, updated_at)
                VALUES (:name, :email, :phone, :company, :notes, :created_at, :updated_at)";

        $now = TimezoneHelper::nowUtc();

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'notes' => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function update(int $id, string $name, string $email, string $phone, string $company, string $status, string $notes): bool
    {
        $sql = "UPDATE customers SET name = :name, email = :email, phone = :phone,
                company = :company, status = :status, notes = :notes, updated_at = :updated_at
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'status' => $status,
            'notes' => $notes,
            'updated_at' => TimezoneHelper::nowUtc(),
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM customers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $query): array
    {
        $sql = "SELECT * FROM customers
                WHERE name LIKE :query OR email LIKE :query OR company LIKE :query
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%{$query}%"]);

        $customers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = $this->mapRowToCustomer($row);
        }

        return $customers;
    }

    public function countByStatus(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM customers GROUP BY status";
        $stmt = $this->db->query($sql);

        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $counts[$row['status']] = (int) $row['count'];
        }

        return $counts;
    }

    private function mapRowToCustomer(array $row): Customer
    {
        return new Customer(
            id: (int) $row['id'],
            name: $row['name'],
            email: $row['email'],
            phone: $row['phone'] ?? '',
            company: $row['company'] ?? '',
            status: $row['status'],
            notes: $row['notes'] ?? '',
            created_at: $row['created_at'],
            updated_at: $row['updated_at'],
        );
    }
}
