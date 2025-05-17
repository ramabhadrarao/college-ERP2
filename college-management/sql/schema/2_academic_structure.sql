-- Academic Structure Schema
-- This file contains the database schema for the academic organization structure

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    college_id INT,
    hod_id INT NULL,
    logo VARCHAR(255),
    description TEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    established_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (college_id) REFERENCES college(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Programs table
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    department_id INT,
    coordinator_id INT NULL,
    duration VARCHAR(50),
    degree_type VARCHAR(50),
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Branches table
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    program_id INT,
    coordinator_id INT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Regulations table
CREATE TABLE IF NOT EXISTS regulations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    program_id INT,
    branch_id INT,
    effective_from_year YEAR,
    effective_to_year YEAR,
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Semesters table
CREATE TABLE IF NOT EXISTS semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    academic_year_id INT NOT NULL,
    regulation_id INT,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20) DEFAULT 'upcoming',
    FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Batches table
CREATE TABLE IF NOT EXISTS batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    program_id INT,
    branch_id INT,
    start_year YEAR NOT NULL,
    end_year YEAR NOT NULL,
    mentor_id INT NULL,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student types table
CREATE TABLE IF NOT EXISTS student_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default student types
INSERT INTO student_types (name, description) VALUES
('Day Scholar', 'Students who commute daily'),
('Hosteler', 'Students residing in hostel'),
('Day Scholar College Bus', 'Day scholars using college bus facility');

-- Add initial semester
INSERT INTO semesters (name, academic_year_id, start_date, end_date, status)
VALUES ('Fall 2023', 1, '2023-06-01', '2023-12-31', 'active');

-- Insert default data
INSERT INTO departments (name, code, college_id, description, status)
VALUES 
('Computer Science', 'CS', 1, 'Department of Computer Science and Engineering', 'active'),
('Electronics', 'ECE', 1, 'Department of Electronics and Communication Engineering', 'active'),
('Mechanical', 'ME', 1, 'Department of Mechanical Engineering', 'active');

INSERT INTO programs (name, code, department_id, duration, degree_type, description, status)
VALUES 
('B.Tech Computer Science', 'BTCS', 1, '4 years', 'Bachelor\'s', 'Bachelor of Technology in Computer Science and Engineering', 'active'),
('M.Tech Computer Science', 'MTCS', 1, '2 years', 'Master\'s', 'Master of Technology in Computer Science and Engineering', 'active'),
('B.Tech Electronics', 'BTEC', 2, '4 years', 'Bachelor\'s', 'Bachelor of Technology in Electronics and Communication Engineering', 'active');

INSERT INTO branches (name, code, program_id, description, status)
VALUES 
('Computer Science', 'CS', 1, 'Computer Science and Engineering', 'active'),
('Artificial Intelligence', 'AI', 1, 'Artificial Intelligence and Machine Learning', 'active'),
('VLSI Design', 'VLSI', 3, 'Very-Large-Scale Integration', 'active');

INSERT INTO regulations (name, code, program_id, branch_id, effective_from_year, effective_to_year, status)
VALUES 
('R2023', 'R2023-BTCS', 1, 1, 2023, 2027, 'active'),
('R2023', 'R2023-BTCS-AI', 1, 2, 2023, 2027, 'active'),
('R2023', 'R2023-BTEC-VLSI', 3, 3, 2023, 2027, 'active');

INSERT INTO batches (name, program_id, branch_id, start_year, end_year, status)
VALUES 
('BTCS 2023-27', 1, 1, 2023, 2027, 'active'),
('BTCS-AI 2023-27', 1, 2, 2023, 2027, 'active'),
('BTEC-VLSI 2023-27', 3, 3, 2023, 2027, 'active');
