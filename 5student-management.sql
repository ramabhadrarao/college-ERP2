-- Student Management: Student records and related entities
-- Contains tables for student data management

-- Enhanced Students Table for better self-management
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id UUID NOT NULL UNIQUE,
    admission_no VARCHAR(50) NOT NULL UNIQUE,
    regd_no VARCHAR(50) UNIQUE,
    name VARCHAR(255) NOT NULL,
    gender_id INT NOT NULL,
    dob DATE,
    email VARCHAR(255) NOT NULL,
    mobile VARCHAR(15),
    batch_id INT NOT NULL,
    program_id INT NOT NULL,
    branch_id INT NOT NULL,
    regulation_id INT NOT NULL,
    current_semester_id INT,
    father_name VARCHAR(255),
    mother_name VARCHAR(255),
    father_mobile VARCHAR(15),
    mother_mobile VARCHAR(15),
    address TEXT,
    permanent_address TEXT,
    nationality_id INT NOT NULL,
    religion_id INT NOT NULL,
    student_type_id INT NOT NULL,
    caste_id INT,
    sub_caste_id INT,
    blood_group_id INT,
    photo_attachment_id UUID,
    aadhar_attachment_id UUID,
    father_aadhar_attachment_id UUID,
    mother_aadhar_attachment_id UUID,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key references
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE CASCADE,
    FOREIGN KEY (current_semester_id) REFERENCES semesters(id) ON DELETE SET NULL,
    FOREIGN KEY (blood_group_id) REFERENCES blood_groups(id),
    FOREIGN KEY (gender_id) REFERENCES gender(id),
    FOREIGN KEY (student_type_id) REFERENCES student_types(id),
    FOREIGN KEY (nationality_id) REFERENCES nationality(id),
    FOREIGN KEY (religion_id) REFERENCES religion(id),
    FOREIGN KEY (caste_id) REFERENCES caste(id),
    FOREIGN KEY (sub_caste_id) REFERENCES sub_caste(id)
);

-- Student Electives table
CREATE TABLE student_electives (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    elective_group_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    selected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved BOOLEAN DEFAULT FALSE,
    approved_by INT, -- Faculty who approved
    approved_at TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (elective_group_id) REFERENCES elective_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Student Educational Details Table
CREATE TABLE student_educational_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    edu_course_name VARCHAR(255) NOT NULL,
    year_of_passing YEAR NOT NULL,
    class_division VARCHAR(50) NOT NULL,
    percentage_grade VARCHAR(50) NOT NULL,
    board_university VARCHAR(255) NOT NULL,
    district_id INT,
    state_id INT,
    subjects_offered TEXT NOT NULL,
    certificate_attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE SET NULL
);

-- Student Additional Documents Table
CREATE TABLE student_additional_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    document_name VARCHAR(255) NOT NULL,
    student_id INT,
    attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Student Course Registration Table
CREATE TABLE student_course_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    registration_type VARCHAR(20) DEFAULT 'Regular',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved BOOLEAN DEFAULT FALSE,
    approved_by INT, -- Faculty who approved
    approved_at TIMESTAMP,
    status VARCHAR(20) DEFAULT 'registered',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Student Certificates table
CREATE TABLE student_certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    certificate_type VARCHAR(20) NOT NULL,
    certificate_number VARCHAR(50),
    issue_date DATE NOT NULL,
    issued_by INT NOT NULL,
    purpose VARCHAR(255),
    attachment_id UUID, -- Generated certificate
    status VARCHAR(20) DEFAULT 'pending',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Student Support Requests table
CREATE TABLE support_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    request_type VARCHAR(20) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    attachment_id UUID,
    status VARCHAR(20) DEFAULT 'open',
    assigned_to INT, -- Staff member assigned to handle
    resolution TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Support Request Comments table
CREATE TABLE support_request_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    user_id UUID NOT NULL,
    comment TEXT NOT NULL,
    attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES support_requests(id) ON DELETE CASCADE
);

-- Student Disciplinary Records
CREATE TABLE student_disciplinary_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    incident_date DATE NOT NULL,
    incident_description TEXT NOT NULL,
    action_taken TEXT,
    severity VARCHAR(20) NOT NULL,
    reported_by INT NOT NULL, -- User who reported
    attachment_id UUID,
    status VARCHAR(20) DEFAULT 'pending',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Student Progress Tracking table
CREATE TABLE student_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    module_id INT NOT NULL,
    material_id INT,
    last_accessed TIMESTAMP,
    completion_status VARCHAR(20) DEFAULT 'not_started',
    completion_date TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES course_materials(id) ON DELETE SET NULL
);

-- Student Feedback table (for course evaluation)
CREATE TABLE student_feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    faculty_id INT NOT NULL,
    semester_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    rating_teaching INT, -- Scale 1-5
    rating_content INT, -- Scale 1-5
    rating_materials INT, -- Scale 1-5
    rating_overall INT, -- Scale 1-5
    suggestions TEXT,
    is_anonymous BOOLEAN DEFAULT TRUE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);
