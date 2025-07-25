-- Student Information Management System Database
-- Create database
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    date_of_birth DATE NOT NULL,
    course VARCHAR(100) NOT NULL,
    grade DECIMAL(5,2) NOT NULL,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample student data
INSERT INTO students (name, email, date_of_birth, course, grade) VALUES
('John Doe', 'john.doe@email.com', '2000-05-15', 'Computer Science', 85.50),
('Jane Smith', 'jane.smith@email.com', '1999-08-22', 'Mathematics', 92.75),
('Mike Johnson', 'mike.johnson@email.com', '2001-03-10', 'Physics', 78.25),
('Sarah Wilson', 'sarah.wilson@email.com', '2000-11-30', 'Chemistry', 88.00),
('David Brown', 'david.brown@email.com', '1999-12-05', 'Biology', 91.25);

-- Create indexes for better performance
CREATE INDEX idx_student_email ON students(email);
CREATE INDEX idx_student_name ON students(name);
CREATE INDEX idx_student_course ON students(course); 