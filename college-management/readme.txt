# College Management System

A comprehensive PHP-based College Management System with user management, role-based permissions, and a modular design to handle various aspects of educational institution management.

## Features

- User Management with role-based permissions
- Dynamic menu generation based on user roles
- Academic structure (Departments, Programs, Branches, etc.)
- Student and Faculty Management
- Course Management
- Responsive UI with Tabler

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. **Clone or download the repository**
   
   Place the files in your web server's document root directory.

2. **Create a database**
   
   Create a new MySQL database for the application.

3. **Configure the database connection**
   
   Edit the file `config/database.php` and update the database credentials:

   ```php
   // Database credentials
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_database_username');
   define('DB_PASS', 'your_database_password');
   define('DB_NAME', 'your_database_name');
   ```

4. **Configure the application**

   Edit the file `config/functions.php` and update the base URL:

   ```php
   define('BASE_URL', 'http://localhost/college-management'); // Update this to match your installation
   ```

5. **Run the installation script**

   Access the installation script in your web browser:
   
   ```
   http://your-domain.com/college-management/install.php
   ```
   
   Follow the instructions to complete the installation.

6. **Login with the admin account**

   After installation, you can log in with the admin credentials you provided during installation.

## Modules

The system is organized into the following modules:

- **User Management**: Manage users, roles, and permissions
- **Academic Structure**: Manage departments, programs, branches, batches, etc.
- **Student Management**: Manage students, enrollments, etc.
- **Faculty Management**: Manage faculty members, assignments, etc.
- **Course Management**: Manage courses, modules, materials, etc.
- **Examination System**: Manage exams, grades, results, etc.
- **Library Management**: Manage books, transactions, etc.
- **Hostel Management**: Manage hostel facilities, allocations, etc.
- **Transportation**: Manage transport routes, vehicles, etc.
- **Finance**: Manage fee structures, invoices, payments, etc.
- **Notification & Communication**: Manage announcements, notifications, etc.

## Directory Structure

```
/
├── auth/                   # Authentication pages
├── config/                 # Configuration files
├── includes/               # Common include files
├── menus/                  # Menu management
├── menu-items/             # Menu items management
├── permissions/            # Permission management
├── roles/                  # Role management
├── sql/                    # SQL scripts
│   ├── data/               # Default data scripts
│   └── schema/             # Schema scripts
├── users/                  # User management
├── index.php               # Dashboard/home page
└── install.php             # Installation script
```

## Usage

1. **User Management**
   - Create users with different roles
   - Manage permissions for each role
   - View user activity and manage accounts

2. **Menu Management**
   - Create and customize menus
   - Assign permissions to menu items
   - Organize menu hierarchy

3. **Roles and Permissions**
   - Create custom roles
   - Define granular permissions
   - Assign roles to users

## Security Features

- Password hashing using bcrypt
- Prepared statements to prevent SQL injection
- CSRF protection
- Input sanitization
- Role-based access control
- Account lockout after failed login attempts
- Password reset functionality

## Customization

The system is designed to be modular and easily customizable:

1. **Adding New Modules**
   
   Create new directories for your module and implement the necessary files.

2. **Customizing UI**
   
   The system uses [Tabler UI](https://tabler.io/), which can be customized to fit your institution's branding.

3. **Extending Functionality**
   
   You can extend the existing modules or add new ones based on your requirements.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For issues, feature requests, or questions, please open an issue on the repository.

## Contributing

Contributions are welcome! Please feel free to submit a pull request.
