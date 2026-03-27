-- =========================================
-- DATABASE: restaurant_system
-- =========================================
CREATE DATABASE IF NOT EXISTS restaurant_system;
USE restaurant_system;

-- =========================================
-- ADMINS TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS  admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    last_login DATETIME NULL
);

INSERT INTO admins (username, password_hash, email) VALUES
('admin','$2y$10$e0NRb6qjvL0Xo5UMX1d0QuX8XmztJr6JxqRbU1DpKssGnR82JG7kG','admin@zestrestaurant.com'),
('manager', '$2y$10$e0NRb6qjvL0Xo5UMX1d0QuX8XmztJr6JxqRbU1DpKssGnR82JG7kG', 'manager@zestrestaurant.com'),
('staff1', '$2y$10$e0NRb6qjvL0Xo5UMX1d0QuX8XmztJr6JxqRbU1DpKssGnR82JG7kG', 'staff1@zestrestaurant.com'),
('staff2', '$2y$10$e0NRb6qjvL0Xo5UMX1d0QuX8XmztJr6JxqRbU1DpKssGnR82JG7kG', 'staff2@zestrestaurant.com'),
('supervisor', '$2y$10$e0NRb6qjvL0Xo5UMX1d0QuX8XmztJr6JxqRbU1DpKssGnR82JG7kG', 'supervisor@zestrestaurant.com');

-- =========================================
-- TABLES (RESTAURANT TABLES)
-- =========================================
CREATE TABLE IF NOT EXISTS  tables (
    table_id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('available','occupied') DEFAULT 'available'
);

INSERT INTO tables (table_number, capacity, status) VALUES
('T1', 2, 'available'),
('T2', 4, 'available'),
('T3', 6, 'occupied'),
('T4', 4, 'available'),
('T5', 8, 'occupied');

-- =========================================
-- RESERVATIONS TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS  reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    table_id INT,
    party_size INT NOT NULL,
    reservation_date DATETIME NOT NULL,
    status ENUM('pending','confirmed','cancelled','held') DEFAULT 'pending',
    hold_expires_at DATETIME NULL,

    FOREIGN KEY (table_id) REFERENCES tables(table_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO reservations 
(customer_name, email, phone, table_id, party_size, reservation_date, status, hold_expires_at)
VALUES
('John Doe', 'john@example.com', '0712345678', 1, 2, '2026-04-10 18:00:00', 'confirmed', NULL),
('Jane Smith', 'jane@example.com', '0723456789', 2, 4, '2026-04-11 19:00:00', 'pending', NULL),
('Michael Kim', 'kim@example.com', '0734567890', 3, 5, '2026-04-12 20:00:00', 'held', '2026-03-27 23:59:00'),
('Sarah Lee', 'sarah@example.com', '0745678901', 4, 3, '2026-04-13 17:30:00', 'cancelled', NULL),
('David Ochieng', 'david@example.com', '0756789012', 5, 6, '2026-04-14 21:00:00', 'confirmed', NULL);

-- =========================================
-- PAYMENTS TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending','completed','failed') DEFAULT 'pending',
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id)
        ON DELETE CASCADE
);

INSERT INTO payments (reservation_id, amount, payment_status) VALUES
(1, 2000.00, 'completed'),
(2, 1500.00, 'pending'),
(3, 1800.00, 'pending'),
(4, 1200.00, 'failed'),
(5, 2500.00, 'completed');