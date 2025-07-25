<?php
/**
 * Application Configuration
 * Main configuration file with settings, error handling, and session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/error.log');

// Application constants
define('APP_NAME', 'Student Information Management System');
define('APP_VERSION', '1.0');
define('UPLOAD_DIR', 'uploads/');
define('LOGS_DIR', 'logs/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Create directories if they don't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
if (!is_dir(LOGS_DIR)) {
    mkdir(LOGS_DIR, 0755, true);
}

/**
 * Custom error handler
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " - Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($error_message, 3, LOGS_DIR . 'error.log');
    
    if (ini_get('display_errors')) {
        echo "<div style='color: red; background: #ffe6e6; padding: 10px; margin: 10px; border: 1px solid #ff9999;'>";
        echo "<strong>Error:</strong> $errstr<br>";
        echo "<strong>File:</strong> $errfile<br>";
        echo "<strong>Line:</strong> $errline";
        echo "</div>";
    }
    
    return true;
}

// Set custom error handler
set_error_handler('customErrorHandler');

/**
 * Custom exception handler
 */
function customExceptionHandler($exception) {
    $error_message = date('Y-m-d H:i:s') . " - Exception: " . $exception->getMessage() . 
                    " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error_message, 3, LOGS_DIR . 'error.log');
    
    if (ini_get('display_errors')) {
        echo "<div style='color: red; background: #ffe6e6; padding: 10px; margin: 10px; border: 1px solid #ff9999;'>";
        echo "<strong>Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine();
        echo "</div>";
    }
}

// Set custom exception handler
set_exception_handler('customExceptionHandler');

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?error=login_required');
        exit();
    }
}

/**
 * Set user preference cookie
 * @param string $name Cookie name
 * @param string $value Cookie value
 * @param int $expiry Expiry time in seconds
 */
function setUserPreference($name, $value, $expiry = 86400) {
    setcookie($name, $value, time() + $expiry, '/');
}

/**
 * Get user preference from cookie
 * @param string $name Cookie name
 * @param string $default Default value if cookie not found
 * @return string
 */
function getUserPreference($name, $default = '') {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

/**
 * Log activity to file
 * @param string $action Action performed
 * @param string $details Additional details
 */
function logActivity($action, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " - $action";
    if (!empty($details)) {
        $log_entry .= " - $details";
    }
    $log_entry .= "\n";
    
    file_put_contents(LOGS_DIR . 'activity.log', $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Sanitize input data
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 * @param string $email Email address
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate date format
 * @param string $date Date string
 * @param string $format Date format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Get current theme from cookie
 * @return string
 */
function getCurrentTheme() {
    return getUserPreference('theme', 'light');
}

/**
 * Set theme preference
 * @param string $theme Theme name
 */
function setTheme($theme) {
    setUserPreference('theme', $theme);
}
?> 