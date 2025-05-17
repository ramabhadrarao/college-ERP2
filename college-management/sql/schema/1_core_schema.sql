-- Core Schema
-- This file contains the core database schema for the system

-- College information table
CREATE TABLE IF NOT EXISTS college (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    logo VARCHAR(255),
    website VARCHAR(255),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- System Settings table
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_group VARCHAR(50),
    is_public BOOLEAN DEFAULT FALSE,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initialize important system settings
INSERT INTO system_settings (setting_key, setting_value, setting_group, description) VALUES
('institute_name', 'College Management System', 'general', 'Name of the educational institution'),
('institute_address', 'Your College Address', 'general', 'Address of the educational institution'),
('institute_phone', '+1234567890', 'general', 'Contact phone number'),
('institute_email', 'info@example.com', 'general', 'Contact email address'),
('academic_year_current', '1', 'academic', 'Current academic year ID'),
('semester_current', '1', 'academic', 'Current semester ID'),
('attendance_minimum_percentage', '75', 'academic', 'Minimum attendance percentage required'),
('grading_system', 'letter', 'academic', 'Grading system type (letter, percentage, gpa)'),
('enable_faculty_self_service', 'true', 'features', 'Enable faculty self-service portal'),
('enable_student_self_service', 'true', 'features', 'Enable student self-service portal'),
('enable_online_fee_payment', 'true', 'features', 'Enable online fee payment feature'),
('enable_elective_selection', 'true', 'features', 'Enable elective selection by students');

-- Reference tables for commonly used data
CREATE TABLE IF NOT EXISTS blood_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group VARCHAR(5) NOT NULL UNIQUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gender (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS nationality (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS religion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS caste (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(50),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sub_caste (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    caste_id INT,
    FOREIGN KEY (caste_id) REFERENCES caste(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    country VARCHAR(50) DEFAULT 'India',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    state_id INT,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default reference data
INSERT INTO blood_groups (blood_group) VALUES
('A+'), ('A-'), ('B+'), ('B-'), ('AB+'), ('AB-'), ('O+'), ('O-');

INSERT INTO gender (name) VALUES
('Male'), ('Female'), ('Other');

-- Academic Years table (to organize semesters)
CREATE TABLE IF NOT EXISTS academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year_name VARCHAR(20) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'upcoming',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms table for classroom management
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL,
    building VARCHAR(100),
    floor INT,
    room_type VARCHAR(20) DEFAULT 'Classroom',
    capacity INT,
    has_projector BOOLEAN DEFAULT FALSE,
    has_computer BOOLEAN DEFAULT FALSE,
    has_ac BOOLEAN DEFAULT FALSE,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initialize first academic year
INSERT INTO academic_years (year_name, start_date, end_date, status)
VALUES ('2023-2024', '2023-06-01', '2024-05-31', 'active');

-- Initialize default college
INSERT INTO college (name, code, website, address, phone, email, status)
VALUES ('College Management System', 'CMS', 'https://example.com', '123 Education Street, Academic City', '+1234567890', 'info@example.com', 'active');
