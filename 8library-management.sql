-- Library Management: Library books and transactions
-- Contains tables for managing library resources

-- Library Books table
CREATE TABLE library_books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publisher VARCHAR(255),
    isbn VARCHAR(20),
    edition VARCHAR(20),
    publication_year YEAR,
    subject VARCHAR(100),
    description TEXT,
    pages INT,
    copies_available INT DEFAULT 0,
    total_copies INT DEFAULT 0,
    location_shelf VARCHAR(50),
    cover_image_attachment_id UUID,
    status VARCHAR(20) DEFAULT 'available',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Library Book Transactions table
CREATE TABLE library_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    user_id UUID NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    renewed_count INT DEFAULT 0,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    fine_paid BOOLEAN DEFAULT FALSE,
    payment_id INT, -- Reference to payment if fine paid
    remarks TEXT,
    status VARCHAR(20) DEFAULT 'issued',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE
);

-- Library Book Categories
CREATE TABLE library_book_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    parent_id INT,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES library_book_categories(id) ON DELETE SET NULL
);

-- Book Category Relations (many-to-many)
CREATE TABLE book_category_relations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    category_id INT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES library_book_categories(id) ON DELETE CASCADE
);

-- Library Book Authors (for multiple authors)
CREATE TABLE library_book_authors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    author_name VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE
);

-- Library Reservations
CREATE TABLE library_reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    user_id UUID NOT NULL,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATE,
    status VARCHAR(20) DEFAULT 'active', -- active, fulfilled, cancelled, expired
    notification_sent BOOLEAN DEFAULT FALSE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE
);

-- Library Settings
CREATE TABLE library_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default library settings
INSERT INTO library_settings (setting_key, setting_value, description) VALUES
('max_books_student', '3', 'Maximum number of books a student can issue'),
('max_books_faculty', '5', 'Maximum number of books a faculty can issue'),
('loan_period_student', '14', 'Loan period in days for students'),
('loan_period_faculty', '30', 'Loan period in days for faculty'),
('fine_per_day', '5.00', 'Fine amount per day for late returns'),
('max_renewals', '2', 'Maximum number of times a book can be renewed'),
('reservation_expiry_days', '3', 'Number of days a reservation remains active');

-- Library Book Inventory History
CREATE TABLE library_inventory_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    action_type VARCHAR(20) NOT NULL, -- added, removed, lost, damaged
    quantity INT NOT NULL,
    action_by UUID NOT NULL,
    remarks TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE
);
