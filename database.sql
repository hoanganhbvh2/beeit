CREATE DATABASE IF NOT EXISTS club_members;

USE club_members;

CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    specialization VARCHAR(100),
    image VARCHAR(255)
);

-- Thêm cột specialization nếu nó chưa tồn tại
ALTER TABLE members
ADD COLUMN specialization VARCHAR(100) AFTER phone;

-- Thêm cột image nếu nó chưa tồn tại
ALTER TABLE members
ADD COLUMN image VARCHAR(255) AFTER specialization;

CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    batch INT NOT NULL,
    migration_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
