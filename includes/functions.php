<?php
/**
 * Utility Functions
 * Common functions for student management operations
 */

require_once 'config/config.php';
require_once 'config/database.php';

/**
 * Get all students from database
 * @return array Array of student records
 */
function getAllStudents() {
    $sql = "SELECT * FROM students ORDER BY name ASC";
    $result = executeQuery($sql);
    
    if ($result) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    }
    
    return [];
}

/**
 * Get student by ID
 * @param int $id Student ID
 * @return array|false Student record or false if not found
 */
function getStudentById($id) {
    $sql = "SELECT * FROM students WHERE id = ?";
    $result = executeQuery($sql, [$id]);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Add new student
 * @param array $data Student data
 * @return bool Success status
 */
function addStudent($data) {
    try {
        // Validate required fields
        $required_fields = ['name', 'email', 'date_of_birth', 'course', 'grade'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate email
        if (!validateEmail($data['email'])) {
            throw new Exception("Invalid email format");
        }
        
        // Validate date
        if (!validateDate($data['date_of_birth'])) {
            throw new Exception("Invalid date format");
        }
        
        // Validate grade
        if (!is_numeric($data['grade']) || $data['grade'] < 0 || $data['grade'] > 100) {
            throw new Exception("Grade must be a number between 0 and 100");
        }
        
        // Check if email already exists
        $check_sql = "SELECT id FROM students WHERE email = ?";
        $check_result = executeQuery($check_sql, [$data['email']]);
        if ($check_result && $check_result->num_rows > 0) {
            throw new Exception("Email already exists");
        }
        
        // Handle file upload
        $profile_picture = '';
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $profile_picture = handleFileUpload($_FILES['profile_picture']);
        }
        
        // Insert student
        $sql = "INSERT INTO students (name, email, date_of_birth, course, grade, profile_picture) VALUES (?, ?, ?, ?, ?, ?)";
        $result = executeQuery($sql, [
            $data['name'],
            $data['email'],
            $data['date_of_birth'],
            $data['course'],
            $data['grade'],
            $profile_picture
        ]);
        
        if ($result) {
            logActivity('Student added', "Name: {$data['name']}, Email: {$data['email']}");
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error adding student: " . $e->getMessage());
        return false;
    }
}

/**
 * Update student
 * @param int $id Student ID
 * @param array $data Updated student data
 * @return bool Success status
 */
function updateStudent($id, $data) {
    try {
        // Validate required fields
        $required_fields = ['name', 'email', 'date_of_birth', 'course', 'grade'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate email
        if (!validateEmail($data['email'])) {
            throw new Exception("Invalid email format");
        }
        
        // Validate date
        if (!validateDate($data['date_of_birth'])) {
            throw new Exception("Invalid date format");
        }
        
        // Validate grade
        if (!is_numeric($data['grade']) || $data['grade'] < 0 || $data['grade'] > 100) {
            throw new Exception("Grade must be a number between 0 and 100");
        }
        
        // Check if email already exists for other students
        $check_sql = "SELECT id FROM students WHERE email = ? AND id != ?";
        $check_result = executeQuery($check_sql, [$data['email'], $id]);
        if ($check_result && $check_result->num_rows > 0) {
            throw new Exception("Email already exists");
        }
        
        // Handle file upload
        $profile_picture = '';
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $profile_picture = handleFileUpload($_FILES['profile_picture']);
        }
        
        // Update student
        if (!empty($profile_picture)) {
            $sql = "UPDATE students SET name = ?, email = ?, date_of_birth = ?, course = ?, grade = ?, profile_picture = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['date_of_birth'], $data['course'], $data['grade'], $profile_picture, $id];
        } else {
            $sql = "UPDATE students SET name = ?, email = ?, date_of_birth = ?, course = ?, grade = ? WHERE id = ?";
            $params = [$data['name'], $data['email'], $data['date_of_birth'], $data['course'], $data['grade'], $id];
        }
        
        $result = executeQuery($sql, $params);
        
        if ($result) {
            logActivity('Student updated', "ID: $id, Name: {$data['name']}");
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error updating student: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete student
 * @param int $id Student ID
 * @return bool Success status
 */
function deleteStudent($id) {
    try {
        // Get student info for logging
        $student = getStudentById($id);
        if (!$student) {
            throw new Exception("Student not found");
        }
        
        // Delete student
        $sql = "DELETE FROM students WHERE id = ?";
        $result = executeQuery($sql, [$id]);
        
        if ($result) {
            logActivity('Student deleted', "ID: $id, Name: {$student['name']}");
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error deleting student: " . $e->getMessage());
        return false;
    }
}

/**
 * Search students
 * @param string $search_term Search term
 * @return array Array of matching students
 */
function searchStudents($search_term) {
    $search_term = '%' . $search_term . '%';
    $sql = "SELECT * FROM students WHERE name LIKE ? OR email LIKE ? OR course LIKE ? ORDER BY name ASC";
    $result = executeQuery($sql, [$search_term, $search_term, $search_term]);
    
    if ($result) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    }
    
    return [];
}

/**
 * Handle file upload
 * @param array $file Uploaded file array
 * @return string|false Filename on success, false on failure
 */
function handleFileUpload($file) {
    try {
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception("File size exceeds limit");
        }
        
        // Check file extension
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, ALLOWED_EXTENSIONS)) {
            throw new Exception("Invalid file type");
        }
        
        // Generate unique filename
        $filename = uniqid() . '.' . $file_extension;
        $upload_path = UPLOAD_DIR . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return $filename;
        }
        
        throw new Exception("Failed to move uploaded file");
    } catch (Exception $e) {
        error_log("File upload error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get available courses
 * @return array Array of course names
 */
function getAvailableCourses() {
    return [
        'Computer Science',
        'Mathematics',
        'Physics',
        'Chemistry',
        'Biology',
        'Engineering',
        'Business Administration',
        'Arts and Humanities',
        'Social Sciences',
        'Medicine'
    ];
}

/**
 * Calculate student statistics
 * @return array Statistics data
 */
function getStudentStatistics() {
    $sql = "SELECT 
                COUNT(*) as total_students,
                AVG(grade) as average_grade,
                MIN(grade) as min_grade,
                MAX(grade) as max_grade,
                COUNT(DISTINCT course) as total_courses
            FROM students";
    
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return [
        'total_students' => 0,
        'average_grade' => 0,
        'min_grade' => 0,
        'max_grade' => 0,
        'total_courses' => 0
    ];
}

/**
 * Format grade for display
 * @param float $grade Grade value
 * @return string Formatted grade
 */
function formatGrade($grade) {
    return number_format($grade, 2) . '%';
}

/**
 * Get grade color based on value
 * @param float $grade Grade value
 * @return string CSS color class
 */
function getGradeColor($grade) {
    if ($grade >= 90) return 'text-success';
    if ($grade >= 80) return 'text-primary';
    if ($grade >= 70) return 'text-warning';
    return 'text-danger';
}

/**
 * Validate user credentials
 * @param string $username Username
 * @param string $password Password
 * @return bool Success status
 */
function validateUser($username, $password) {
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $result = executeQuery($sql, [$username]);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            logActivity('User login', "Username: $username");
            return true;
        }
    }
    
    return false;
}
?> 