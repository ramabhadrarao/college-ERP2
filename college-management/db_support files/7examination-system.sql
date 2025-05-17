-- Examination System: Exam scheduling, marks, and results
-- Contains tables for managing exams and student performance

-- Exam Types table
CREATE TABLE exam_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL, -- e.g., Internal 1, Internal 2, Semester End
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Exam Schedule table
CREATE TABLE exam_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_type_id INT NOT NULL,
    course_id INT,
    semester_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    exam_date DATE,
    exam_start_time TIME,
    exam_end_time TIME,
    exam_venue VARCHAR(255),
    faculty_assigned INT, -- Faculty supervising
    max_marks INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_type_id) REFERENCES exam_types(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_assigned) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Student Exam Marks table
CREATE TABLE student_exam_marks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    exam_type_id INT,
    semester_id INT,
    academic_year_id INT,
    marks_obtained DECIMAL(7,2),
    max_marks DECIMAL(7,2),
    remarks TEXT,
    recorded_by INT, -- Faculty who recorded
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_type_id) REFERENCES exam_types(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Grade Scale table
CREATE TABLE grade_scale (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grade CHAR(2) NOT NULL,
    min_marks DECIMAL(5,2) NOT NULL,
    max_marks DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(3,2) NOT NULL,
    description VARCHAR(50),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student Course Results table
CREATE TABLE student_course_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    semester_id INT,
    academic_year_id INT,
    total_marks DECIMAL(7,2),
    grade CHAR(2),
    grade_point DECIMAL(3,2),
    credits_earned DECIMAL(5,2),
    result_status VARCHAR(20) DEFAULT 'Incomplete',
    recorded_by INT, -- Faculty who recorded
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_by INT, -- Faculty who verified
    verified_at TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES faculty(id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Semester Results Summary table
CREATE TABLE semester_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    semester_id INT,
    academic_year_id INT,
    total_credits DECIMAL(5,2),
    credits_earned DECIMAL(5,2),
    sgpa DECIMAL(4,2), -- Semester GPA
    result_status VARCHAR(20) DEFAULT 'Incomplete',
    remarks TEXT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Cumulative Results table
CREATE TABLE cumulative_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    up_to_semester_id INT, -- Results calculated up to this semester
    total_credits DECIMAL(5,2),
    credits_earned DECIMAL(5,2),
    cgpa DECIMAL(4,2), -- Cumulative GPA
    result_status VARCHAR(20) DEFAULT 'In Progress',
    remarks TEXT,
    last_updated TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (up_to_semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Question Types table
CREATE TABLE question_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Quizzes table
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructions TEXT,
    start_datetime TIMESTAMP,
    end_datetime TIMESTAMP,
    time_limit INT, -- Time limit in minutes
    total_marks INT,
    passing_percentage INT DEFAULT 35,
    shuffle_questions BOOLEAN DEFAULT FALSE,
    show_results_after VARCHAR(20) DEFAULT 'immediately',
    status VARCHAR(20) DEFAULT 'draft',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

-- Quiz Questions table
CREATE TABLE quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type_id INT NOT NULL,
    image_attachment_id UUID,
    correct_answer TEXT,
    explanation TEXT,
    marks INT NOT NULL,
    difficulty VARCHAR(20) DEFAULT 'medium',
    order_index INT DEFAULT 0,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (question_type_id) REFERENCES question_types(id) ON DELETE CASCADE
);

-- Quiz Options table
CREATE TABLE quiz_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    explanation TEXT,
    order_index INT DEFAULT 0,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

-- Quiz Attempts table
CREATE TABLE quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    student_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP,
    time_taken INT, -- In seconds
    is_completed BOOLEAN DEFAULT FALSE,
    total_marks_obtained DECIMAL(7,2) DEFAULT 0,
    percentage_score DECIMAL(5,2) DEFAULT 0,
    passed BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Quiz Answers table
CREATE TABLE quiz_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_text TEXT,
    selected_option_id INT,
    is_correct BOOLEAN DEFAULT FALSE,
    marks_awarded DECIMAL(7,2) DEFAULT 0,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option_id) REFERENCES quiz_options(id) ON DELETE SET NULL
);

-- Insert default exam types
INSERT INTO exam_types (name, description) VALUES
('Internal Assessment 1', 'First internal assessment'),
('Internal Assessment 2', 'Second internal assessment'),
('Internal Assessment 3', 'Third internal assessment'),
('Mid Semester', 'Mid-semester examination'),
('End Semester', 'End-semester examination'),
('Practical', 'Practical examination'),
('Viva Voce', 'Oral examination');

-- Create default grade scale
INSERT INTO grade_scale (grade, min_marks, max_marks, grade_point, description) VALUES
('O', 90, 100, 10.0, 'Outstanding'),
('A+', 80, 89.99, 9.0, 'Excellent'),
('A', 70, 79.99, 8.0, 'Very Good'),
('B+', 60, 69.99, 7.0, 'Good'),
('B', 50, 59.99, 6.0, 'Above Average'),
('C', 40, 49.99, 5.0, 'Average'),
('P', 35, 39.99, 4.0, 'Pass'),
('F', 0, 34.99, 0.0, 'Fail');

-- Insert default question types
INSERT INTO question_types (name, description) VALUES
('multiple_choice', 'Single correct answer from multiple choices'),
('multiple_answer', 'Multiple correct answers from choices'),
('true_false', 'True or False statement'),
('short_answer', 'Short text answer'),
('essay', 'Long form text answer'),
('matching', 'Match items from two columns'),
('numerical', 'Numerical answer with tolerance');