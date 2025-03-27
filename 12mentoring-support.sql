-- Mentoring & Support: Mentoring sessions and support systems
-- Contains tables for student mentoring and support services

-- Student Mentoring table
CREATE TABLE student_mentoring (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    faculty_id INT NOT NULL, -- Faculty mentor
    academic_year_id INT NOT NULL,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Mentoring Meeting Records
CREATE TABLE mentoring_meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mentoring_id INT NOT NULL,
    meeting_date TIMESTAMP NOT NULL,
    meeting_type VARCHAR(20) DEFAULT 'regular',
    topics_discussed TEXT,
    recommendations TEXT,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_date DATE,
    attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mentoring_id) REFERENCES student_mentoring(id) ON DELETE CASCADE
);

-- Mentoring Goals
CREATE TABLE mentoring_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mentoring_id INT NOT NULL,
    goal_title VARCHAR(255) NOT NULL,
    goal_description TEXT,
    target_completion_date DATE,
    status VARCHAR(20) DEFAULT 'pending',
    completion_date DATE,
    remarks TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mentoring_id) REFERENCES student_mentoring(id) ON DELETE CASCADE
);

-- Student Progress Reports
CREATE TABLE student_progress_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    semester_id INT NOT NULL,
    report_date DATE NOT NULL,
    academic_performance TEXT,
    attendance_summary TEXT,
    extracurricular_activities TEXT,
    areas_of_improvement TEXT,
    recommendations TEXT,
    faculty_id INT NOT NULL, -- Faculty who prepared the report
    status VARCHAR(20) DEFAULT 'draft',
    published_date DATE,
    attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Counseling Services
CREATE TABLE counseling_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    service_type VARCHAR(50) NOT NULL, -- academic, personal, career, etc.
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Counselors
CREATE TABLE counselors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id UUID NOT NULL,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    qualification VARCHAR(200),
    contact_no VARCHAR(15),
    email VARCHAR(100),
    available_days VARCHAR(100), -- e.g., "Monday,Wednesday,Friday"
    available_hours VARCHAR(100), -- e.g., "10:00-13:00,14:00-16:00"
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Counseling Appointments
CREATE TABLE counseling_appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    counselor_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status VARCHAR(20) DEFAULT 'scheduled',
    remarks TEXT,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_date DATE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (counselor_id) REFERENCES counselors(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES counseling_services(id) ON DELETE CASCADE
);

-- Student Career Development
CREATE TABLE career_development (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL, -- workshop, internship, job fair, etc.
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    organizer VARCHAR(100),
    location VARCHAR(100),
    skills_gained TEXT,
    certificate_attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Career Workshops
CREATE TABLE career_workshops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    workshop_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    venue VARCHAR(100),
    speaker_name VARCHAR(100),
    speaker_profile TEXT,
    max_participants INT,
    registration_deadline DATE,
    targeted_batch_id INT,
    targeted_program_id INT,
    status VARCHAR(20) DEFAULT 'upcoming',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (targeted_batch_id) REFERENCES batches(id) ON DELETE SET NULL,
    FOREIGN KEY (targeted_program_id) REFERENCES programs(id) ON DELETE SET NULL
);

-- Workshop Registrations
CREATE TABLE workshop_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workshop_id INT NOT NULL,
    student_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attendance_status VARCHAR(20) DEFAULT 'registered',
    feedback TEXT,
    rating INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_id) REFERENCES career_workshops(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);