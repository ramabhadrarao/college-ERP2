-- Default Menus
-- This file contains SQL statements to create the default menu structure

-- Create the main sidebar menu
INSERT INTO menus (name, description) VALUES 
('main_sidebar', 'Main navigation sidebar menu'),
('user_menu', 'User profile dropdown menu');

-- Create main sidebar menu items
-- Dashboard
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), NULL, 'Dashboard', '/index.php', 'ti ti-home', 1, 1);

-- User Management section
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), NULL, 'User Management', '', 'ti ti-users', 10, 1);

-- Get the User Management parent ID
SET @user_mgmt_id = LAST_INSERT_ID();

-- User Management child items
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), @user_mgmt_id, 'Users', '/users/index.php', 'ti ti-user', 1, 1),
((SELECT id FROM menus WHERE name = 'main_sidebar'), @user_mgmt_id, 'Roles', '/roles/index.php', 'ti ti-shield', 2, 1),
((SELECT id FROM menus WHERE name = 'main_sidebar'), @user_mgmt_id, 'Permissions', '/permissions/index.php', 'ti ti-lock', 3, 1);

-- System Settings section
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), NULL, 'System Settings', '', 'ti ti-settings', 20, 1);

-- Get the System Settings parent ID
SET @sys_settings_id = LAST_INSERT_ID();

-- System Settings child items
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), @sys_settings_id, 'Menus', '/menus/index.php', 'ti ti-menu-2', 1, 1),
((SELECT id FROM menus WHERE name = 'main_sidebar'), @sys_settings_id, 'System Configuration', '/settings/index.php', 'ti ti-adjustments', 2, 1);

-- Academic Structure section
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), NULL, 'Academic Structure', '', 'ti ti-building', 30, 1);

-- Get the Academic Structure parent ID
SET @academic_struct_id = LAST_INSERT_ID();

-- Academic Structure child items
INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active) VALUES
((SELECT id FROM menus WHERE name = 'main_sidebar'), @academic_struct_id, 'Departments', '/departments/index.php', 'ti ti-building-arch', 1, 1),
((SELECT id FROM menus WHERE name = 'main_sidebar'), @academic_struct_id, 'Programs', '/programs/index.php', 'ti ti-certificate', 2, 1),
((SELECT id FROM menus WHERE name = 'main_sidebar'), @academic_struct_id, 'Branches', '/branches/index.php', 'ti ti-git-branch', 3, 1),
((SELECT id FROM menus WHERE name = 'main_sidebar'), @academic_struct_id, 'Batches', '/batches/index.php', 'ti ti-users-group', 4, 1);

-- Assign permissions to menu items (Users section)
INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Users' AND parent_id = @user_mgmt_id),
    id
FROM permissions 
WHERE name = 'user_view';

-- Assign permissions to menu items (Roles section)
INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Roles' AND parent_id = @user_mgmt_id),
    id
FROM permissions 
WHERE name = 'role_view';

-- Assign permissions to menu items (Permissions section)
INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Permissions' AND parent_id = @user_mgmt_id),
    id
FROM permissions 
WHERE name = 'permission_view';

-- Assign permissions to menu items (Menus section)
INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Menus' AND parent_id = @sys_settings_id),
    id
FROM permissions 
WHERE name = 'menu_view';

-- Assign permissions to menu items (System Configuration section)
INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'System Configuration' AND parent_id = @sys_settings_id),
    id
FROM permissions 
WHERE name = 'settings_view';

-- Assign permissions to menu items (Academic Structure items)
INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Departments' AND parent_id = @academic_struct_id),
    id
FROM permissions 
WHERE name = 'department_manage';

INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Programs' AND parent_id = @academic_struct_id),
    id
FROM permissions 
WHERE name = 'program_manage';

INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Branches' AND parent_id = @academic_struct_id),
    id
FROM permissions 
WHERE name = 'branch_manage';

INSERT INTO menu_item_permissions (menu_item_id, permission_id) 
SELECT 
    (SELECT id FROM menu_items WHERE title = 'Batches' AND parent_id = @academic_struct_id),
    id
FROM permissions 
WHERE name = 'batch_manage';