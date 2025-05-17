-- Default roles and permissions
-- This file contains SQL statements to create default roles and permissions

-- Default Permissions
INSERT INTO permissions (name, description, module) VALUES
-- User Management
('user_view', 'View user list and details', 'UserManagement'),
('user_create', 'Create new users', 'UserManagement'),
('user_edit', 'Edit existing users', 'UserManagement'),
('user_delete', 'Delete users', 'UserManagement'),

-- Role Management
('role_view', 'View role list and details', 'RoleManagement'),
('role_create', 'Create new roles', 'RoleManagement'),
('role_edit', 'Edit existing roles', 'RoleManagement'),
('role_delete', 'Delete roles', 'RoleManagement'),

-- Permission Management
('permission_view', 'View permission list and details', 'PermissionManagement'),
('permission_create', 'Create new permissions', 'PermissionManagement'),
('permission_edit', 'Edit existing permissions', 'PermissionManagement'),
('permission_delete', 'Delete permissions', 'PermissionManagement'),

-- Menu Management
('menu_view', 'View menu list and details', 'MenuManagement'),
('menu_create', 'Create new menus', 'MenuManagement'),
('menu_edit', 'Edit existing menus', 'MenuManagement'),
('menu_delete', 'Delete menus', 'MenuManagement'),
('menu_item_manage', 'Manage menu items', 'MenuManagement'),

-- Student Management
('student_view', 'View student list and details', 'StudentManagement'),
('student_create', 'Create new students', 'StudentManagement'),
('student_edit', 'Edit existing students', 'StudentManagement'),
('student_delete', 'Delete students', 'StudentManagement'),

-- Course Management
('course_view', 'View course list and details', 'CourseManagement'),
('course_create', 'Create new courses', 'CourseManagement'),
('course_edit', 'Edit existing courses', 'CourseManagement'),
('course_delete', 'Delete courses', 'CourseManagement'),

-- Faculty Management
('faculty_view', 'View faculty list and details', 'FacultyManagement'),
('faculty_create', 'Create new faculty', 'FacultyManagement'),
('faculty_edit', 'Edit existing faculty', 'FacultyManagement'),
('faculty_delete', 'Delete faculty', 'FacultyManagement'),

-- Academic Structure
('department_manage', 'Manage departments', 'AcademicStructure'),
('program_manage', 'Manage programs', 'AcademicStructure'),
('branch_manage', 'Manage branches', 'AcademicStructure'),
('batch_manage', 'Manage batches', 'AcademicStructure'),

-- System Settings
('settings_view', 'View system settings', 'SystemSettings'),
('settings_edit', 'Edit system settings', 'SystemSettings');

-- Default Roles
INSERT INTO roles (name, description, is_system_role) VALUES
('Admin', 'Administrator with full access to all system features', 1),
('Manager', 'System manager with access to most system features', 0),
('Faculty', 'Faculty members with access to academic features', 0),
('Student', 'Students with limited access to view their information', 0),
('Registrar', 'Manages student records and academic information', 0),
('Library', 'Manages library resources and transactions', 0);

-- Assign permissions to Admin role
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Admin'),
    id,
    NOW()
FROM permissions;

-- Assign permissions to Manager role
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Manager'),
    id,
    NOW()
FROM permissions
WHERE name IN (
    'user_view', 'user_create', 'user_edit',
    'role_view', 
    'permission_view',
    'menu_view',
    'student_view', 'student_create', 'student_edit',
    'course_view', 'course_create', 'course_edit',
    'faculty_view', 'faculty_create', 'faculty_edit',
    'department_manage', 'program_manage', 'branch_manage', 'batch_manage',
    'settings_view'
);

-- Assign permissions to Faculty role
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Faculty'),
    id,
    NOW()
FROM permissions
WHERE name IN (
    'student_view',
    'course_view'
);

-- Assign permissions to Student role
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Student'),
    id,
    NOW()
FROM permissions
WHERE name IN (
    'course_view'
);

-- Assign permissions to Registrar role
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Registrar'),
    id,
    NOW()
FROM permissions
WHERE name IN (
    'student_view', 'student_create', 'student_edit',
    'course_view',
    'department_manage', 'program_manage', 'branch_manage', 'batch_manage'
);

-- Assign permissions to Library role
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Library'),
    id,
    NOW()
FROM permissions
WHERE name IN (
    'student_view'
);