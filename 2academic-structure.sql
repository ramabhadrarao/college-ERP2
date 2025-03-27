-- Academic Structure: Programs, departments, branches, etc.
-- Contains tables for academic organizational structure

-- Departments table with additional fields for self-management
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    college_id INT,
    hod_id INT NULL, -- Head of Department reference to faculty table
    logo VARCHAR(255),
    description TEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    established_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (college_id) REFERENCES college(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Programs table (structured course of study leading to a degree)
CREATE TABLE programs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    department_id INT,
    coordinator_id INT NULL, -- Program coordinator reference to faculty table
    duration VARCHAR(50), -- e.g., "4 years", "2 years"
    degree_type VARCHAR(50), -- e.g., "Bachelor's", "Master's", "Doctoral"
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Branches table
CREATE TABLE branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    program_id INT,
    coordinator_id INT NULL, -- Branch coordinator reference to faculty table
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Regulations table with enhanced metadata
CREATE TABLE regulations (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Semesters table with academic year reference
CREATE TABLE semesters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    academic_year_id INT NOT NULL,
    regulation_id INT,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20) DEFAULT 'upcoming',
    FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Batches table
CREATE TABLE batches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    program_id INT,
    branch_id INT,
    start_year YEAR NOT NULL,
    end_year YEAR NOT NULL,
    mentor_id INT NULL, -- Faculty mentor for the batch
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student types table
CREATE TABLE student_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default student types
INSERT INTO student_types (name, description) VALUES
('Day Scholar', 'Students who commute daily'),
('Hosteler', 'Students residing in hostel'),
('Day Scholar College Bus', 'Day scholars using college bus facility');

-- Add initial semester
INSERT INTO semesters (name, academic_year_id, start_date, end_date, status)
VALUES ('Fall 2024', 1, '2024-06-01', '2024-12-31', 'active');
