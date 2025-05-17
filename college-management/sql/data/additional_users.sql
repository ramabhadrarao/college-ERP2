-- Example of additional_users.sql
INSERT INTO users (id, username, password_hash, email, is_active, is_verified, created_at, updated_at) 
VALUES 
(UUID(), 'teacher1', '$2y$12$...[bcrypt hash]...', 'teacher1@example.com', 1, 1, NOW(), NOW()),
(UUID(), 'student1', '$2y$12$...[bcrypt hash]...', 'student1@example.com', 1, 1, NOW(), NOW());

-- Assign roles
INSERT INTO user_roles (user_id, role_id, created_at)
SELECT 
    (SELECT id FROM users WHERE username = 'teacher1'), 
    (SELECT id FROM roles WHERE name = 'Faculty'),
    NOW();