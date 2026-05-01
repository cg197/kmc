-- KABWE MUNICIPAL COUNCIL

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE DATABASE IF NOT EXISTS kabwe_council_db;
USE kabwe_council_db;

-- Table: users

CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'resident') DEFAULT 'resident',
    phone_number VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: properties

CREATE TABLE IF NOT EXISTS properties (
    property_id INT(11) NOT NULL AUTO_INCREMENT,
    owner_id INT(11) NOT NULL,
    plot_number VARCHAR(50) UNIQUE NOT NULL,
    area_name VARCHAR(100) NOT NULL,
    annual_rates DECIMAL(10, 2) DEFAULT 0.00,
    PRIMARY KEY (property_id),
    CONSTRAINT fk_property_owner FOREIGN KEY (owner_id) 
        REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table: service_requests store

CREATE TABLE IF NOT EXISTS service_requests (
    request_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    service_type ENUM('Waste Collection', 'Building Permit', 'Trading License', 'Water Connection') NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (request_id),
    CONSTRAINT fk_request_user FOREIGN KEY (user_id) 
        REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table: payments check

CREATE TABLE IF NOT EXISTS payments (
    payment_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_type VARCHAR(50) NOT NULL,
    method ENUM('Mobile Money', 'Bank Transfer', 'Cash') NOT NULL,
    reference_no VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('pending', 'verified', 'failed') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (payment_id),
    CONSTRAINT fk_payment_user FOREIGN KEY (user_id) 
        REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table: documents upload

CREATE TABLE IF NOT EXISTS documents (
    doc_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    doc_type VARCHAR(50) DEFAULT 'Other',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (doc_id),
    CONSTRAINT fk_document_user FOREIGN KEY (user_id) 
        REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- admin_dashboard

CREATE OR REPLACE VIEW admin_dashboard_summary AS
SELECT 
    u.full_name AS 'Resident',
    sr.service_type AS 'Service',
    sr.status AS 'App_Status',
    p.amount AS 'Payment_Received',
    p.status AS 'Payment_Status',
    sr.date_submitted AS 'Date'
FROM users u
INNER JOIN service_requests sr ON u.user_id = sr.user_id
LEFT JOIN payments p ON u.user_id = p.user_id
WHERE sr.deleted_at IS NULL;

COMMIT;