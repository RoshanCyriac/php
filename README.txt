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
- Environment-based configuration

QUICK START:
===========
```bash
# 1. Clone or download the project
cd /path/to/project

# 2. Set up environment
cp .env.example .env
# Edit .env file with your MySQL credentials

# 3. Set up database
mysql -u root -p < database.sql

# 4. Set permissions
chmod 755 uploads/ logs/

# 5. Start the server
php -S localhost:8000

# 6. Open browser
# http://localhost:8000
# Login: admin/admin123
```

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

5. RUNNING THE APPLICATION:
   
   OPTION 1: PHP Built-in Server (Recommended for Development)
   -----------------------------------------------------------
   ```bash
   # Navigate to project directory
   cd /path/to/your/project
   
   # Start PHP development server
   php -S localhost:8000
   
   # Access the application
   Open browser: http://localhost:8000
   ```
   
   OPTION 2: Apache/Nginx Web Server (Production)
   ----------------------------------------------
   ```bash
   # Copy files to web server directory
   sudo cp -r . /var/www/html/student-management/
   
   # Set proper permissions
   sudo chown -R www-data:www-data /var/www/html/student-management/
   sudo chmod -R 755 /var/www/html/student-management/
   
   # Access the application
   Open browser: http://localhost/student-management/
   ```

6. LOGIN CREDENTIALS:
   - Username: admin
   - Password: admin123

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

TROUBLESHOOTING:
===============

Common Issues and Solutions:

1. Database Connection Error:
   - Verify MySQL is running: `sudo systemctl status mysql`
   - Check credentials in `.env` file
   - Ensure database exists: `mysql -u root -p -e "SHOW DATABASES;"`

2. Permission Errors:
   - Set proper permissions: `chmod 755 uploads/ logs/`
   - Check ownership: `ls -la uploads/ logs/`

3. File Upload Issues:
   - Check upload directory permissions
   - Verify file size limits in `.env`
   - Check PHP upload settings in php.ini

4. Login Issues:
   - Verify admin user exists: `mysql -u root -p -e "USE student_management; SELECT * FROM users;"`
   - Reset password if needed

5. Server Not Starting:
   - Check if port 8000 is available: `netstat -tlnp | grep :8000`
   - Try different port: `php -S localhost:8080`

USAGE GUIDE:
===========
1. Login with admin credentials
2. Add students with profile pictures
3. View and search student records
4. Edit or delete student information
5. View system logs and activity
6. Switch between light/dark themes

AUTHOR: Student Information Management System
VERSION: 1.0
DATE: 2024 
