-- Create Database
CREATE DATABASE IF NOT EXISTS queue_db;
USE queue_db;

-- Table: departments
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    prefix VARCHAR(10) NOT NULL,
    window_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: staff
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Table: queue_tickets
CREATE TABLE IF NOT EXISTS queue_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(20) NOT NULL UNIQUE,
    student_name VARCHAR(100) NOT NULL,
    student_number VARCHAR(50),
    department_id INT NOT NULL,
    is_priority BOOLEAN DEFAULT FALSE,
    status ENUM('waiting', 'serving', 'completed', 'cancelled', 'no-show', 'skipped', 'transferred') DEFAULT 'waiting',
    called_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    staff_id INT NULL,
    transferred_to_department_id INT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (staff_id) REFERENCES staff(id),
    FOREIGN KEY (transferred_to_department_id) REFERENCES departments(id)
);

-- Insert default departments
INSERT INTO departments (name, prefix, window_number) VALUES
('Cashier', 'C', 1),
('Admission', 'A', 2),
('Registrar', 'R', 3);

-- Insert a default staff user (password: admin123)
INSERT INTO staff (username, password, full_name, department_id) VALUES
-- password: admin123
('admin', '$2y$10$G8P7M9YcJFx9X/9x7E4jTeV6s68S3dQ9vq6vWTdW9B5Y7QnXH3U9K', 'Admin User', 1);
