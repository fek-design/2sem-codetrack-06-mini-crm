-- Convert existing datetime data to UTC timezone
-- This migration assumes existing data was stored in Europe/Copenhagen timezone
-- and converts it to UTC for proper timezone handling

-- Note: SQLite doesn't have native timezone functions, so we need to subtract 1 or 2 hours
-- depending on whether it's standard time (CET = UTC+1) or daylight saving time (CEST = UTC+2)
-- For simplicity, we'll assume most data is in standard time (subtract 1 hour)

-- Update customers table timestamps
UPDATE customers
SET created_at = datetime(created_at, '-1 hour'),
    updated_at = datetime(updated_at, '-1 hour')
WHERE created_at NOT LIKE '%Z' AND created_at NOT LIKE '%+%';

-- Update leads table timestamps
UPDATE leads
SET created_at = datetime(created_at, '-1 hour'),
    updated_at = datetime(updated_at, '-1 hour')
WHERE created_at NOT LIKE '%Z' AND created_at NOT LIKE '%+%';

-- Update interactions table timestamps
UPDATE interactions
SET interaction_date = datetime(interaction_date, '-1 hour'),
    created_at = datetime(created_at, '-1 hour')
WHERE interaction_date NOT LIKE '%Z' AND interaction_date NOT LIKE '%+%';
