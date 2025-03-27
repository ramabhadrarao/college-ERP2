-- Curriculum: Courses, course types, modules, materials
-- Contains tables for curriculum management

-- Course Types table
CREATE TABLE course_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,  -- e.g., Theory, Lab, Elective, Open Elective
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table with improved structure for self-management
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    semester_id INT,
    branch_id INT,
    regulation_id INT NOT NULL,
    course_type_id INT NOT NULL,
    credits INT NOT NULL,
    syllabus TEXT,
    description TEXT,
    objectives TEXT,
    outcomes TEXT,
    prerequisites TEXT,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL,
    FOREIGN KEY (regulation_id) REFERENCES regulations(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (course_type_id) REFERENCES course_types(id) ON DELETE CASCADE
);

-- Course Coordinator table to track faculty assigned to courses
CREATE TABLE course_coordinators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    faculty_id INT NOT NULL,
    semester_id INT NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Mark Types table
CREATE TABLE mark_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,  -- e.g., Internal, External, Assignment
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Marks Distribution table
CREATE TABLE marks_distribution (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    mark_type_id INT NOT NULL,
    marks INT NOT NULL,
    description TEXT,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (mark_type_id) REFERENCES mark_types(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Elective Groups table
CREATE TABLE elective_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    semester_id INT NOT NULL,
    elective_type VARCHAR(20) NOT NULL,
    min_credits INT DEFAULT 0, -- Minimum credits required
    max_courses INT DEFAULT 1, -- Maximum courses a student can select
    description TEXT,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Elective Group Courses table
CREATE TABLE elective_group_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    elective_group_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (elective_group_id) REFERENCES elective_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Elective Eligibility table
CREATE TABLE elective_eligibility (
    id INT PRIMARY KEY AUTO_INCREMENT,
    elective_group_id INT NOT NULL,
    program_id INT NOT NULL,
    FOREIGN KEY (elective_group_id) REFERENCES elective_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Module Types table
CREATE TABLE module_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Course Modules table
CREATE TABLE course_modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    faculty_id INT, -- Who created this module
    title VARCHAR(255) NOT NULL,
    description TEXT,
    module_type_id INT NOT NULL,
    order_index INT DEFAULT 0, -- For ordering modules
    status VARCHAR(20) DEFAULT 'draft',
    start_date TIMESTAMP, -- When this module becomes available
    end_date TIMESTAMP, -- When this module expires
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (module_type_id) REFERENCES module_types(id) ON DELETE CASCADE
);

-- Material Types table
CREATE TABLE material_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    mime_type VARCHAR(100),
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Course Materials table
CREATE TABLE course_materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    attachment_id UUID,
    material_type_id INT NOT NULL,
    order_index INT DEFAULT 0, -- For ordering materials
    status VARCHAR(20) DEFAULT 'draft',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE,
    FOREIGN KEY (material_type_id) REFERENCES material_types(id) ON DELETE CASCADE
);

-- Assignments table
CREATE TABLE assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructions TEXT,
    start_date TIMESTAMP, -- When assignment becomes available
    due_date TIMESTAMP, -- Submission deadline
    max_marks INT,
    attachment_id UUID, -- For assignment instructions file
    allow_late_submission BOOLEAN DEFAULT FALSE,
    late_submission_penalty INT DEFAULT 0, -- Percentage penalty
    status VARCHAR(20) DEFAULT 'draft',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

-- Assignment Submissions table
CREATE TABLE assignment_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submission_text TEXT,
    attachment_id UUID,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_late BOOLEAN DEFAULT FALSE,
    marks_awarded DECIMAL(7,2) DEFAULT NULL,
    feedback TEXT DEFAULT NULL,
    graded_by INT, -- Faculty who graded
    graded_at TIMESTAMP,
    status VARCHAR(20) DEFAULT 'submitted',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
);

-- Insert default module types
INSERT INTO module_types (name, description) VALUES
('lesson', 'Regular lesson content'),
('assignment', 'Student assignments'),
('quiz', 'Online quizzes and tests'),
('forum', 'Discussion forums'),
('resource', 'Additional learning resources'),
('media', 'Video or audio content');

-- Insert default material types
INSERT INTO material_types (name, mime_type, description) VALUES
('document', 'application/pdf', 'PDF Documents'),
('presentation', 'application/vnd.ms-powerpoint', 'PowerPoint Presentations'),
('spreadsheet', 'application/vnd.ms-excel', 'Excel Spreadsheets'),
('video', 'video/mp4', 'Video Files'),
('audio', 'audio/mpeg', 'Audio Files'),
('image', 'image/jpeg', 'Image Files'),
('link', 'text/url', 'Web Links'),
('text', 'text/plain', 'Plain Text');

-- Insert default course types
INSERT INTO course_types (name, description) VALUES
('theory', 'Theory-based courses'),
('practical', 'Laboratory/Practical courses'),
('project', 'Project-based courses'),
('seminar', 'Seminar-based courses'),
('core', 'Core/Mandatory courses'),
('elective', 'Elective courses'),
('open_elective', 'Open Elective courses across departments');

-- Insert default mark types
INSERT INTO mark_types (name, description) VALUES
('internal', 'Internal assessment marks'),
('external', 'External/University exam marks'),
('assignment', 'Assignment marks'),
('practical', 'Practical/Lab marks'),
('project', 'Project work marks'),
('attendance', 'Attendance component'),
('mid_term', 'Mid-term examination marks'),
('end_term', 'End-term examination marks');
