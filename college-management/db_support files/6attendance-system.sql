-- Attendance System: Attendance tracking and reporting
-- Contains tables for monitoring and managing student attendance

-- Enhanced Attendance table with more useful data
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    faculty_id INT, -- Who marked the attendance
    semester_id INT,
    academic_year_id INT,
    attendance_date DATE,
    period INT, -- Which period of the day
    status VARCHAR(20) DEFAULT 'Absent',
    remarks TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE SET NULL,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Attendance Summary table for easy reporting
CREATE TABLE attendance_summary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    semester_id INT,
    academic_year_id INT,
    total_classes INT DEFAULT 0,
    classes_attended INT DEFAULT 0,
    attendance_percentage DECIMAL(5,2) DEFAULT 0.00,
    last_updated TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Enhanced Timetable table
CREATE TABLE timetable (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    faculty_id INT,
    semester_id INT,
    academic_year_id INT,
    room_id INT, -- Reference to the rooms table
    day_of_week VARCHAR(20),
    period INT, -- Which period of the day
    start_time TIME,
    end_time TIME,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- Attendance View for summary reporting
-- This is a creation of a view that will make attendance reporting easier
CREATE VIEW attendance_summary_view AS
SELECT 
    s.id AS student_id,
    s.name AS student_name,
    c.course_code,
    c.name AS course_name,
    COUNT(DISTINCT a.attendance_date) AS total_classes,
    COUNT(DISTINCT CASE WHEN a.status = 'Present' THEN a.attendance_date END) AS classes_attended,
    ROUND(COUNT(DISTINCT CASE WHEN a.status = 'Present' THEN a.attendance_date END) / 
          COUNT(DISTINCT a.attendance_date) * 100, 2) AS attendance_percentage,
    CASE 
        WHEN ROUND(COUNT(DISTINCT CASE WHEN a.status = 'Present' THEN a.attendance_date END) / 
                  COUNT(DISTINCT a.attendance_date) * 100, 2) < 
             (SELECT setting_value FROM system_settings WHERE setting_key = 'attendance_minimum_percentage')
        THEN 'At Risk'
        ELSE 'Good Standing'
    END AS attendance_status
FROM 
    students s
JOIN 
    attendance a ON s.id = a.student_id
JOIN 
    courses c ON a.course_id = c.id
GROUP BY 
    s.id, student_name, c.course_code, c.name;
