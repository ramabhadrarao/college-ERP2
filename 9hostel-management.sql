-- Hostel Management: Hostel buildings, rooms, and allocations
-- Contains tables for managing hostel facilities

-- Hostel Buildings table
CREATE TABLE hostel_buildings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    building_type VARCHAR(20) NOT NULL,
    address TEXT,
    total_rooms INT DEFAULT 0,
    warden_name VARCHAR(100),
    warden_contact VARCHAR(15),
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hostel Rooms table
CREATE TABLE hostel_rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    building_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    floor INT,
    room_type VARCHAR(20) NOT NULL,
    capacity INT DEFAULT 1,
    occupied INT DEFAULT 0,
    has_attached_bathroom BOOLEAN DEFAULT FALSE,
    has_ac BOOLEAN DEFAULT FALSE,
    monthly_rent DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'available',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES hostel_buildings(id) ON DELETE CASCADE
);

-- Hostel Allocations table
CREATE TABLE hostel_allocations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE,
    status VARCHAR(20) DEFAULT 'current',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES hostel_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Hostel Complaints table
CREATE TABLE hostel_complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    building_id INT NOT NULL,
    room_id INT,
    complaint_type VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    attachment_id UUID,
    status VARCHAR(20) DEFAULT 'pending',
    assigned_to UUID, -- Staff assigned to handle
    resolution TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES hostel_buildings(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES hostel_rooms(id) ON DELETE SET NULL
);

-- Hostel Room History
CREATE TABLE hostel_room_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    action_type VARCHAR(20) NOT NULL, -- maintenance, cleaning, renovation
    start_date DATE NOT NULL,
    end_date DATE,
    description TEXT,
    performed_by UUID,
    status VARCHAR(20) DEFAULT 'pending',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES hostel_rooms(id) ON DELETE CASCADE
);

-- Hostel Visitor Log
CREATE TABLE hostel_visitor_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    visitor_contact VARCHAR(15),
    relation VARCHAR(50),
    purpose VARCHAR(100),
    check_in TIMESTAMP NOT NULL,
    check_out TIMESTAMP,
    remarks TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Hostel Inventory
CREATE TABLE hostel_inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    building_id INT,
    room_id INT,
    item_name VARCHAR(100) NOT NULL,
    item_type VARCHAR(50),
    quantity INT NOT NULL,
    condition VARCHAR(20) DEFAULT 'good',
    purchase_date DATE,
    last_maintenance_date DATE,
    next_maintenance_date DATE,
    remarks TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES hostel_buildings(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES hostel_rooms(id) ON DELETE SET NULL
);

-- Hostel Rules
CREATE TABLE hostel_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    building_id INT, -- NULL means applicable to all buildings
    is_active BOOLEAN DEFAULT TRUE,
    order_index INT DEFAULT 0,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES hostel_buildings(id) ON DELETE CASCADE
);

-- Hostel Attendance
CREATE TABLE hostel_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    building_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'present',
    remarks TEXT,
    recorded_by UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES hostel_buildings(id) ON DELETE CASCADE
);
