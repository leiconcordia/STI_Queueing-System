-- Migration: Add Admin Department
-- Description: Adds a new Admin department to the system
-- Date: 2026-02-23

USE queue_db;

-- Add Admin department if it doesn't exist
INSERT INTO departments (name, prefix, window_number) 
SELECT 'Admin', 'ADM', 4
WHERE NOT EXISTS (
    SELECT 1 FROM departments WHERE name = 'Admin'
);

-- Optional: Create a default admin user for the Admin department
-- Password: admin123 (hashed with bcrypt)
INSERT INTO staff (username, password, full_name, department_id)
SELECT 'superadmin', '$2y$10$G8P7M9YcJFx9X/9x7E4jTeV6s68S3dQ9vq6vWTdW9B5Y7QnXH3U9K', 'Super Admin', 
    (SELECT id FROM departments WHERE name = 'Admin')
WHERE NOT EXISTS (
    SELECT 1 FROM staff WHERE username = 'superadmin'
);
