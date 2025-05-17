-- Notifications & Communication: Notifications, announcements
-- Contains tables for communication within the college

-- Announcements table
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    posted_by UUID NOT NULL, -- User ID who posted
    target_type VARCHAR(20) NOT NULL, -- all, college, department, program, branch, batch, course, faculty, student
    target_id INT, -- ID of the target entity (can be null for 'all')
    start_date TIMESTAMP,
    end_date TIMESTAMP,
    attachment_id UUID,
    is_important BOOLEAN DEFAULT FALSE,
    status VARCHAR(20) DEFAULT 'published',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notification Templates
CREATE TABLE notification_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(100) NOT NULL,
    template_code VARCHAR(50) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables TEXT, -- JSON array of available variables
    sms_template TEXT, -- Shorter version for SMS
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Notifications
CREATE TABLE user_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id UUID NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(50), -- e.g., 'assignment', 'grade', 'announcement'
    related_entity VARCHAR(50), -- e.g., 'courses', 'exams', 'attendance'
    related_id INT, -- ID of the related item
    is_read BOOLEAN DEFAULT FALSE,
    is_email_sent BOOLEAN DEFAULT FALSE,
    is_sms_sent BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP
);

-- Bulk Notifications
CREATE TABLE bulk_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(50) NOT NULL,
    target_type VARCHAR(20) NOT NULL, -- all, department, program, branch, batch, course
    target_id INT, -- ID of the target entity (can be null for 'all')
    created_by UUID NOT NULL,
    send_email BOOLEAN DEFAULT FALSE,
    send_sms BOOLEAN DEFAULT FALSE,
    send_push BOOLEAN DEFAULT TRUE,
    scheduled_for TIMESTAMP,
    sent_at TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pending',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- SMS and Email Logs
CREATE TABLE communication_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    communication_type VARCHAR(20) NOT NULL, -- email, sms, push
    recipient_id UUID, -- User ID if sent to a user
    recipient_address VARCHAR(255), -- Email or phone number
    subject VARCHAR(255),
    message TEXT,
    template_id INT, -- Reference to template if used
    status VARCHAR(20) DEFAULT 'pending',
    error_message TEXT,
    sent_at TIMESTAMP,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES notification_templates(id) ON DELETE SET NULL
);

-- Forum Discussions table
CREATE TABLE forum_discussions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT,
    user_id UUID NOT NULL,
    topic_title VARCHAR(255) NOT NULL,
    topic_content TEXT,
    is_announcement BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    status VARCHAR(20) DEFAULT 'open',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

-- Forum Posts table
CREATE TABLE forum_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    discussion_id INT NOT NULL,
    user_id UUID NOT NULL,
    parent_post_id INT,
    post_content TEXT NOT NULL,
    attachment_id UUID,
    is_solution BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (discussion_id) REFERENCES forum_discussions(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_post_id) REFERENCES forum_posts(id) ON DELETE SET NULL
);

-- Forum Reactions table
CREATE TABLE forum_reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id UUID NOT NULL,
    reaction_type VARCHAR(20) NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reaction (post_id, user_id, reaction_type)
);

-- Chat Groups
CREATE TABLE chat_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_name VARCHAR(100) NOT NULL,
    group_type VARCHAR(50) NOT NULL, -- course, department, project, custom
    related_id INT, -- ID of related entity (course, department, etc.)
    created_by UUID NOT NULL,
    is_private BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Chat Group Members
CREATE TABLE chat_group_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id UUID NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES chat_groups(id) ON DELETE CASCADE
);

-- Chat Messages
CREATE TABLE chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id UUID NOT NULL,
    message TEXT NOT NULL,
    attachment_id UUID,
    is_pinned BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES chat_groups(id) ON DELETE CASCADE
);

-- Insert default notification templates
INSERT INTO notification_templates (template_name, template_code, subject, body, variables, sms_template, is_active) VALUES
('Welcome Email', 'WELCOME_EMAIL', 'Welcome to {{institute_name}}', 'Dear {{name}},\n\nWelcome to {{institute_name}}. Your login credentials are as follows:\n\nUsername: {{username}}\nPassword: {{password}}\n\nPlease change your password upon first login.\n\nRegards,\n{{institute_name}} Team', '["name", "username", "password", "institute_name"]', 'Welcome to {{institute_name}}. Your login: {{username}}, pass: {{password}}. Please change password on first login.', TRUE),
('Assignment Submission', 'ASSIGNMENT_SUBMIT', 'Assignment Submitted: {{assignment_title}}', 'Dear {{name}},\n\nYour submission for the assignment "{{assignment_title}}" has been received successfully.\n\nCourse: {{course_name}}\nSubmission Date: {{submission_date}}\n\nRegards,\n{{institute_name}} Team', '["name", "assignment_title", "course_name", "submission_date", "institute_name"]', 'Your submission for "{{assignment_title}}" has been received successfully.', TRUE),
('Fee Payment', 'FEE_PAYMENT', 'Fee Payment Receipt: {{invoice_number}}', 'Dear {{name}},\n\nWe have received your payment of Rs. {{amount}} towards {{fee_type}} for {{academic_period}}.\n\nInvoice Number: {{invoice_number}}\nPayment Date: {{payment_date}}\nMode of Payment: {{payment_mode}}\n\nRegards,\n{{institute_name}} Team', '["name", "amount", "fee_type", "academic_period", "invoice_number", "payment_date", "payment_mode", "institute_name"]', 'Payment received: Rs.{{amount}} for {{fee_type}}. Invoice: {{invoice_number}}.', TRUE),
('Exam Schedule', 'EXAM_SCHEDULE', 'Exam Schedule: {{exam_type}}', 'Dear {{name}},\n\nThe schedule for {{exam_type}} has been published.\n\nExam Start Date: {{start_date}}\nExam End Date: {{end_date}}\n\nPlease login to your portal to view the detailed schedule.\n\nRegards,\n{{institute_name}} Team', '["name", "exam_type", "start_date", "end_date", "institute_name"]', '{{exam_type}} schedule published. Start: {{start_date}}, End: {{end_date}}. Login for details.', TRUE),
('Attendance Alert', 'ATTENDANCE_ALERT', 'Attendance Alert: Below Required Percentage', 'Dear {{name}},\n\nThis is to inform you that your attendance in {{course_name}} is {{attendance_percentage}}%, which is below the required {{minimum_percentage}}%.\n\nPlease improve your attendance to avoid any academic penalties.\n\nRegards,\n{{institute_name}} Team', '["name", "course_name", "attendance_percentage", "minimum_percentage", "institute_name"]', 'Alert: Your attendance in {{course_name}} is {{attendance_percentage}}%, below required {{minimum_percentage}}%.', TRUE);
