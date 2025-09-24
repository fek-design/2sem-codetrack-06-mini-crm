-- Remove 'prospect' status from customers and convert to 'active'
-- Customers should only have 'active' or 'inactive' status
-- 'prospect' status should only be used for leads

UPDATE customers
SET status = 'active'
WHERE status = 'prospect';

-- Add an interaction log for any customers that were converted from prospect to active
INSERT INTO interactions (customer_id, type, subject, description, interaction_date, created_at)
SELECT
    id as customer_id,
    'note' as type,
    'Status updated' as subject,
    'Status automatically changed from ''prospect'' to ''active'' (prospect status removed from customers)' as description,
    datetime('now') as interaction_date,
    datetime('now') as created_at
FROM customers
WHERE status = 'active'
AND id IN (
    SELECT DISTINCT customer_id
    FROM interactions
    WHERE customer_id IS NOT NULL
    UNION
    SELECT id FROM customers WHERE created_at < datetime('now', '-1 minute')
);
