-- Transportation: Routes, vehicles, and student registrations
-- Contains tables for managing college transportation system

-- Transportation Routes table
CREATE TABLE transportation_routes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    route_number VARCHAR(20),
    start_location VARCHAR(100) NOT NULL,
    end_location VARCHAR(100) NOT NULL,
    distance DECIMAL(8,2), -- in kilometers
    route_map_attachment_id UUID,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Route Stops table
CREATE TABLE route_stops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    route_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    stop_order INT NOT NULL,
    arrival_time TIME,
    departure_time TIME,
    coordinates VARCHAR(50), -- Latitude,Longitude
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES transportation_routes(id) ON DELETE CASCADE
);

-- Transport Vehicles table
CREATE TABLE transport_vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_number VARCHAR(20) NOT NULL,
    vehicle_type VARCHAR(50),
    make VARCHAR(50),
    model VARCHAR(50),
    capacity INT,
    driver_name VARCHAR(100),
    driver_contact VARCHAR(15),
    route_id INT,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES transportation_routes(id) ON DELETE SET NULL
);

-- Transport Registration table for students
CREATE TABLE transport_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    route_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    boarding_stop_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES transportation_routes(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (boarding_stop_id) REFERENCES route_stops(id) ON DELETE CASCADE
);

-- Vehicle Maintenance Records
CREATE TABLE vehicle_maintenance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    maintenance_type VARCHAR(50) NOT NULL,
    maintenance_date DATE NOT NULL,
    next_maintenance_date DATE,
    performed_by VARCHAR(100),
    cost DECIMAL(10,2),
    description TEXT,
    attachment_id UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES transport_vehicles(id) ON DELETE CASCADE
);

-- Transport Fees
CREATE TABLE transport_fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    route_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    per_semester BOOLEAN DEFAULT TRUE,
    effective_from DATE NOT NULL,
    effective_to DATE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES transportation_routes(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Transport Attendance
CREATE TABLE transport_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    route_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'present',
    remarks TEXT,
    recorded_by UUID,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES transportation_routes(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES transport_vehicles(id) ON DELETE CASCADE
);

-- Vehicle GPS Tracking
CREATE TABLE vehicle_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    coordinates VARCHAR(50) NOT NULL, -- Latitude,Longitude
    speed DECIMAL(5,2), -- in km/h
    track_time TIMESTAMP NOT NULL,
    status VARCHAR(20), -- moving, stopped, idle
    route_id INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES transport_vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES transportation_routes(id) ON DELETE SET NULL
);

-- Transport Drivers
CREATE TABLE transport_drivers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    license_expiry DATE NOT NULL,
    contact VARCHAR(15) NOT NULL,
    alternate_contact VARCHAR(15),
    address TEXT,
    photo_attachment_id UUID,
    license_attachment_id UUID,
    joining_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vehicle Driver Assignment
CREATE TABLE vehicle_driver_assignment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    driver_id INT NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE,
    is_primary BOOLEAN DEFAULT TRUE,
    status VARCHAR(20) DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES transport_vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES transport_drivers(id) ON DELETE CASCADE
);
