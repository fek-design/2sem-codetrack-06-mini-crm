-- Fix inconsistent interaction_date formats in existing data
-- Convert HTML5 datetime-local format (2025-09-20T12:00) to standard format (2025-09-20 12:00:00)

UPDATE interactions
SET interaction_date = REPLACE(interaction_date || ':00', 'T', ' ')
WHERE interaction_date LIKE '%T%';
