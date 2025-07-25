<?php
/**
 * Database Configuration
 * Handles MySQL database connection and provides error handling
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_management');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * @return mysqli|false Database connection or false on failure
 */
function getDatabaseConnection() {
    try {
        // Create connection using mysqli
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset
        $conn->set_charset(DB_CHARSET);
        
        return $conn;
    } catch (Exception $e) {
        // Log error
        error_log("Database connection error: " . $e->getMessage());
        return false;
    }
}

/**
 * Close database connection
 * @param mysqli $conn Database connection
 */
function closeDatabaseConnection($conn) {
    if ($conn && $conn instanceof mysqli) {
        $conn->close();
    }
}

/**
 * Execute a prepared statement safely
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return mysqli_result|bool Query result or false on failure
 */
function executeQuery($sql, $params = []) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return false;
    }
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Assume all strings for simplicity
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stmt->close();
        closeDatabaseConnection($conn);
        
        return $result;
    } catch (Exception $e) {
        error_log("Query execution error: " . $e->getMessage());
        closeDatabaseConnection($conn);
        return false;
    }
}
?> 