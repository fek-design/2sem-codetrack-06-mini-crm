-- Create leads table
CREATE TABLE IF NOT EXISTS `leads` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `email` TEXT NOT NULL,
    `phone` TEXT,
    `company` TEXT,
    `source` TEXT NOT NULL DEFAULT 'none',
    `status` TEXT NOT NULL DEFAULT 'new',
    `notes` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
