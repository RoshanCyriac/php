STUDENT INFORMATION MANAGEMENT SYSTEM
=====================================

This is a web-based PHP application that allows users to manage student records with full CRUD operations.

FEATURES:
- Add, view, search, and update student records
- File upload for profile pictures
- Session and cookie management
- Database connectivity with MySQL
- Error and exception handling
- Log file operations
- User authentication

SETUP INSTRUCTIONS:
==================

1. PREREQUISITES:
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Web server (Apache/Nginx)
   - PHP extensions: mysqli, session, fileinfo

2. ENVIRONMENT SETUP:
   - Copy '.env.example' to '.env'
   - Update the database credentials in '.env' file:
     DB_HOST=localhost
     DB_NAME=student_management
     DB_USER=root
     DB_PASS=your_mysql_password
   - Customize other settings as needed

3. DATABASE SETUP:
   - Create a MySQL database named 'student_management'
   - Import the database structure from 'database.sql'

3. FILE PERMISSIONS:
   - Ensure 'uploads/' directory is writable (chmod 755)
   - Ensure 'logs/' directory is writable (chmod 755)

4. CONFIGURATION:
   - All configuration is now handled through the '.env' file
   - No need to edit PHP files for basic configuration

5. ACCESS THE APPLICATION:
   - Place all files in your web server directory
   - Access via: http://localhost/your-project-folder/
   - Default login: admin/admin123

PROJECT STRUCTURE:
=================
├── index.php              # Login and home page
├── add_student.php        # Add student form
├── view_students.php      # View all students
├── search_student.php     # Search students
├── logs.php              # View log files
├── logout.php            # Logout functionality
├── config/
│   ├── database.php      # Database configuration
│   └── config.php        # Application configuration
├── includes/
│   ├── header.php        # Common header
│   ├── footer.php        # Common footer
│   └── functions.php     # Utility functions
├── uploads/              # Profile picture uploads
├── logs/                 # Application logs
└── database.sql          # Database structure

ENVIRONMENT VARIABLES:
=====================
The application uses environment variables for configuration:
- Database settings (host, name, user, password)
- Application settings (name, version, file paths)
- Upload settings (max file size, allowed extensions)
- Error reporting settings

All sensitive data is stored in the '.env' file (not committed to version control).

SECURITY FEATURES:
=================
- Session-based authentication
- SQL injection prevention
- File upload validation
- Error handling and logging
- Input validation and sanitization
- Environment-based configuration

TECHNICAL REQUIREMENTS:
======================
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- PHP extensions: mysqli, session, fileinfo

AUTHOR: Student Information Management System
VERSION: 1.0
DATE: 2024 