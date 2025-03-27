-- Faculty Management: Faculty profiles and related entities
-- Contains tables for faculty data management

-- Enhanced Faculty table for self-management
CREATE TABLE faculty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id UUID NOT NULL UNIQUE,
    regdno VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50),
    gender_id INT,
    dob DATE,
    contact_no VARCHAR(15),
    email VARCHAR(100) UNIQUE NOT NULL,
    department_id INT,
    designation VARCHAR(100),
    qualification VARCHAR(255),
    specialization TEXT,
    join_date DATE NOT NULL,
    address TEXT,
    blood_group_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    edit_enabled BOOLEAN DEFAULT TRUE,
    aadhar_attachment_id UUID,
    pan_attachment_id UUID,
    photo_attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (gender_id) REFERENCES gender(id),
    FOREIGN KEY (blood_group_id) REFERENCES blood_groups(id),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Faculty Additional Details Table
CREATE TABLE faculty_additional_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    father_name VARCHAR(255),
    father_occupation VARCHAR(255),
    mother_name VARCHAR(255),
    mother_occupation VARCHAR(255),
    marital_status VARCHAR(20),
    spouse_name VARCHAR(255),
    spouse_occupation VARCHAR(255),
    nationality_id INT,
    religion_id INT,
    caste_id INT,
    sub_caste_id INT,
    aadhar_no VARCHAR(20),
    pan_no VARCHAR(20),
    contact_no2 VARCHAR(20),
    permanent_address TEXT,
    correspondence_address TEXT,
    scopus_author_id VARCHAR(255),
    orcid_id VARCHAR(255),
    google_scholar_id_link VARCHAR(255),
    aicte_id VARCHAR(255),
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (nationality_id) REFERENCES nationality(id),
    FOREIGN KEY (religion_id) REFERENCES religion(id),
    FOREIGN KEY (caste_id) REFERENCES caste(id),
    FOREIGN KEY (sub_caste_id) REFERENCES sub_caste(id)
);

-- Faculty Work Experience table
CREATE TABLE work_experiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    institution_name VARCHAR(255) NOT NULL,
    experience_type VARCHAR(20) NOT NULL,
    designation VARCHAR(255),
    from_date DATE,
    to_date DATE,
    number_of_years DECIMAL(5,2),
    responsibilities TEXT,
    service_certificate_attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Faculty Qualifications table
CREATE TABLE faculty_qualifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    degree VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    institution VARCHAR(200) NOT NULL,
    board_university VARCHAR(200),
    passing_year YEAR,
    percentage_cgpa VARCHAR(20),
    certificate_attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Publication Type Table
CREATE TABLE publication_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Intellectual Property Status Table
CREATE TABLE intellectual_property_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Funding Agency Table
CREATE TABLE funding_agency (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    agency_type VARCHAR(50),
    website VARCHAR(100),
    contact_info TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Workshop Type Table
CREATE TABLE workshop_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- MDP/FDP Type Table
CREATE TABLE mdp_fdp_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Award Category Table
CREATE TABLE award_category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Conference Role Table
CREATE TABLE conference_role (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Faculty Teaching Activities Table
CREATE TABLE teaching_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    course_name VARCHAR(200) NOT NULL,
    semester_id INT,
    academic_year_id INT,
    course_code VARCHAR(20),
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE SET NULL
);

-- Research Publications Table
CREATE TABLE research_publications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    journal_name VARCHAR(200),
    type_id INT,
    publication_date DATE,
    doi VARCHAR(50),
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    citations INT DEFAULT 0,
    impact_factor DECIMAL(5,2),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES publication_type(id) ON DELETE SET NULL
);

-- Books and Chapters Table
CREATE TABLE books_and_chapters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    chapter_title VARCHAR(200),
    publisher VARCHAR(100),
    publication_year YEAR,
    isbn VARCHAR(20),
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Conference Proceedings Table
CREATE TABLE conference_proceedings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    conference_title VARCHAR(200) NOT NULL,
    location VARCHAR(100),
    conference_date DATE,
    paper_title VARCHAR(200),
    role_id INT,
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES conference_role(id) ON DELETE SET NULL
);

-- Honours and Awards Table
CREATE TABLE honours_awards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    award_title VARCHAR(200) NOT NULL,
    awarded_by VARCHAR(200),
    award_date DATE,
    category_id INT,
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES award_category(id) ON DELETE SET NULL
);

-- Intellectual Property Table
CREATE TABLE intellectual_property (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    type VARCHAR(20),
    patent_app_no VARCHAR(50),
    filing_date DATE,
    grant_date DATE,
    status_id INT,
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES intellectual_property_status(id) ON DELETE SET NULL
);

-- Research and Consultancy Projects Table
CREATE TABLE research_consultancy (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    project_title VARCHAR(200) NOT NULL,
    project_type VARCHAR(20),
    agency_id INT,
    grant_amount DECIMAL(12,2),
    start_date DATE,
    end_date DATE,
    status VARCHAR(20),
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (agency_id) REFERENCES funding_agency(id) ON DELETE SET NULL
);

-- Workshops and Seminars Table
CREATE TABLE workshops_seminars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    type_id INT,
    location VARCHAR(100),
    organized_by VARCHAR(200),
    start_date DATE,
    end_date DATE,
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES workshop_type(id) ON DELETE SET NULL
);

-- MDP/FDP Details Table
CREATE TABLE mdp_fdp (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    type_id INT,
    location VARCHAR(100),
    organized_by VARCHAR(200),
    start_date DATE,
    end_date DATE,
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES mdp_fdp_type(id) ON DELETE SET NULL
);

-- Other Professional Activities Table
CREATE TABLE professional_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    activity_title VARCHAR(200) NOT NULL,
    activity_type VARCHAR(100),
    role VARCHAR(100),
    organization VARCHAR(200),
    activity_date DATE,
    description TEXT,
    attachment_id UUID,
    visibility VARCHAR(10) DEFAULT 'show',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);