-- Create interactions table
CREATE TABLE IF NOT EXISTS `interactions` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `customer_id` INTEGER,
    `lead_id` INTEGER,
    `type` TEXT NOT NULL,
    `subject` TEXT NOT NULL,
    `description` TEXT,
    `interaction_date` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Foreign key constraints
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE,

    -- Ensure interaction belongs to either customer or lead, but not both
    CHECK ((`customer_id` IS NOT NULL AND `lead_id` IS NULL) OR (`customer_id` IS NULL AND `lead_id` IS NOT NULL))
);
