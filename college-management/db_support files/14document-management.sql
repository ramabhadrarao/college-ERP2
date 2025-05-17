-- Document Management: Document templates and certificates
-- Contains tables for managing document templates and certificates

-- Document Templates table for various certificates and documents
CREATE TABLE document_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    template_type VARCHAR(20) NOT NULL,
    content TEXT NOT NULL,
    variables TEXT, -- List of variables used in the template
    attachment_id UUID, -- Attachment for template files
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Document Categories
CREATE TABLE document_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES document_categories(id) ON DELETE SET NULL
);

-- Document Repository
CREATE TABLE document_repository (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    document_category_id INT,
    attachment_id UUID NOT NULL,
    version VARCHAR(20),
    uploaded_by UUID NOT NULL,
    access_level VARCHAR(20) DEFAULT 'public', -- public, restricted, private
    allowed_roles TEXT, -- JSON array of role IDs if restricted
    is_archived BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (document_category_id) REFERENCES document_categories(id) ON DELETE SET NULL
);

-- Document Access Logs
CREATE TABLE document_access_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    document_id INT NOT NULL,
    user_id UUID NOT NULL,
    action_type VARCHAR(20) NOT NULL, -- view, download, print
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (document_id) REFERENCES document_repository(id) ON DELETE CASCADE
);

-- Certificate Types
CREATE TABLE certificate_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    template_id INT,
    prefix VARCHAR(20), -- For certificate numbering
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES document_templates(id) ON DELETE SET NULL
);

-- Student Certificates
CREATE TABLE certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    certificate_type_id INT NOT NULL,
    certificate_number VARCHAR(50) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    issued_by UUID NOT NULL,
    purpose VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by UUID,
    verified_at TIMESTAMP,
    attachment_id UUID, -- Generated certificate
    status VARCHAR(20) DEFAULT 'issued',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (certificate_type_id) REFERENCES certificate_types(id) ON DELETE CASCADE
);

-- Certificate Verification
CREATE TABLE certificate_verification (
    id INT PRIMARY KEY AUTO_INCREMENT,
    certificate_id INT NOT NULL,
    verification_code VARCHAR(100) NOT NULL UNIQUE,
    verification_url VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by VARCHAR(100), -- Name/Email of external verifier
    verified_at TIMESTAMP,
    ip_address VARCHAR(45),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE
);

-- Digital Signatures
CREATE TABLE digital_signatures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    user_id UUID NOT NULL,
    signature_image_id UUID NOT NULL,
    position VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Transcript Templates
CREATE TABLE transcript_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    header_content TEXT,
    footer_content TEXT,
    css_style TEXT,
    variables TEXT, -- List of variables used in the template
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student Transcripts
CREATE TABLE student_transcripts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    template_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    semester_id INT,
    transcript_number VARCHAR(50) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    issued_by UUID NOT NULL,
    is_official BOOLEAN DEFAULT TRUE,
    attachment_id UUID, -- Generated transcript
    status VARCHAR(20) DEFAULT 'issued',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES transcript_templates(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL
);

-- Student Document Requests
CREATE TABLE document_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    request_type VARCHAR(20) NOT NULL, -- certificate, transcript, etc.
    certificate_type_id INT,
    purpose VARCHAR(255) NOT NULL,
    urgent BOOLEAN DEFAULT FALSE,
    additional_info TEXT,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pending',
    processed_by UUID,
    processed_at TIMESTAMP,
    remarks TEXT,
    document_id INT, -- ID of the generated document
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (certificate_type_id) REFERENCES certificate_types(id) ON DELETE SET NULL
);

-- Insert default document templates
INSERT INTO document_templates (name, template_type, content, variables, is_active) VALUES
('Bonafide Certificate', 'certificate', '<div style="text-align: center;"><h2>{{institute_name}}</h2><h3>BONAFIDE CERTIFICATE</h3></div><p>This is to certify that {{student_name}} (Reg. No: {{registration_number}}) is a bonafide student of this institution studying in {{program}} {{branch}} during the academic year {{academic_year}}.</p><p>This certificate is issued for the purpose of {{purpose}}.</p><div style="margin-top: 50px;"><div style="float: left;">Date: {{issue_date}}</div><div style="float: right;">Signature of Principal</div></div>', '["institute_name", "student_name", "registration_number", "program", "branch", "academic_year", "purpose", "issue_date"]', TRUE),
('Transfer Certificate', 'certificate', '<div style="text-align: center;"><h2>{{institute_name}}</h2><h3>TRANSFER CERTIFICATE</h3></div><p>This is to certify that {{student_name}} (Reg. No: {{registration_number}}) was a student of this institution from {{admission_date}} to {{leaving_date}} studying in {{program}} {{branch}}.</p><p>His/Her character and conduct during the period of study was {{conduct}}.</p><p>Reason for leaving: {{reason}}</p><div style="margin-top: 50px;"><div style="float: left;">Date: {{issue_date}}</div><div style="float: right;">Signature of Principal</div></div>', '["institute_name", "student_name", "registration_number", "admission_date", "leaving_date", "program", "branch", "conduct", "reason", "issue_date"]', TRUE),
('Course Completion Certificate', 'certificate', '<div style="text-align: center;"><h2>{{institute_name}}</h2><h3>COURSE COMPLETION CERTIFICATE</h3></div><p>This is to certify that {{student_name}} (Reg. No: {{registration_number}}) has successfully completed the {{program}} {{branch}} program during the period from {{start_date}} to {{end_date}}.</p><p>This certificate is issued for the purpose of {{purpose}}.</p><div style="margin-top: 50px;"><div style="float: left;">Date: {{issue_date}}</div><div style="float: right;">Signature of Principal</div></div>', '["institute_name", "student_name", "registration_number", "program", "branch", "start_date", "end_date", "purpose", "issue_date"]', TRUE),
('Character Certificate', 'certificate', '<div style="text-align: center;"><h2>{{institute_name}}</h2><h3>CHARACTER CERTIFICATE</h3></div><p>This is to certify that {{student_name}} (Reg. No: {{registration_number}}) was a student of this institution from {{admission_date}} to {{leaving_date}} studying in {{program}} {{branch}}.</p><p>During this period, his/her conduct and character were {{character_description}}.</p><div style="margin-top: 50px;"><div style="float: left;">Date: {{issue_date}}</div><div style="float: right;">Signature of Principal</div></div>', '["institute_name", "student_name", "registration_number", "admission_date", "leaving_date", "program", "branch", "character_description", "issue_date"]', TRUE);

-- Insert default certificate types
INSERT INTO certificate_types (name, description, prefix, is_active) VALUES
('Bonafide Certificate', 'Certificate confirming student status', 'BF', TRUE),
('Transfer Certificate', 'Certificate issued when student leaves institution', 'TC', TRUE),
('Course Completion', 'Certificate confirming course completion', 'CC', TRUE),
('Character Certificate', 'Certificate attesting to student character', 'CHR', TRUE),
('Provisional Degree', 'Provisional degree certificate', 'PD', TRUE),
('Migration Certificate', 'For students moving to another university', 'MG', TRUE);
