-- Migration Script to Update Queue System for History & Logs Feature
-- Run this SQL script to update your existing database

USE queue_db;

-- Add new columns to queue_tickets table
ALTER TABLE queue_tickets 
    MODIFY COLUMN status ENUM('waiting', 'serving', 'completed', 'cancelled', 'no-show', 'skipped', 'transferred') DEFAULT 'waiting',
    ADD COLUMN staff_id INT NULL AFTER completed_at,
    ADD COLUMN transferred_to_department_id INT NULL AFTER staff_id,
    ADD COLUMN notes TEXT NULL AFTER transferred_to_department_id;

-- Add foreign key constraints
ALTER TABLE queue_tickets 
    ADD CONSTRAINT fk_staff FOREIGN KEY (staff_id) REFERENCES staff(id),
    ADD CONSTRAINT fk_transferred_dept FOREIGN KEY (transferred_to_department_id) REFERENCES departments(id);

-- Create index for better query performance on history searches
CREATE INDEX idx_created_at ON queue_tickets(created_at);
CREATE INDEX idx_status ON queue_tickets(status);
CREATE INDEX idx_department_id ON queue_tickets(department_id);

-- Update existing records to set staff_id where applicable (optional)
-- This sets the staff_id to NULL for existing records, which will show "N/A" in history
UPDATE queue_tickets SET staff_id = NULL WHERE staff_id IS NULL;

SELECT 'Migration completed successfully!' as message;
