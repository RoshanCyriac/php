<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Handle theme switching
if (isset($_GET['theme'])) {
    setTheme($_GET['theme']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$current_theme = getCurrentTheme();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?php echo $current_theme === 'dark' ? '#343a40' : '#007bff'; ?>;
            --bg-color: <?php echo $current_theme === 'dark' ? '#212529' : '#ffffff'; ?>;
            --text-color: <?php echo $current_theme === 'dark' ? '#ffffff' : '#212529'; ?>;
            --card-bg: <?php echo $current_theme === 'dark' ? '#343a40' : '#ffffff'; ?>;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        .card {
            background-color: var(--card-bg);
            border: 1px solid <?php echo $current_theme === 'dark' ? '#495057' : '#dee2e6'; ?>;
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
        }
        
        .table {
            color: var(--text-color);
        }
        
        .theme-switch {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }
        
        .theme-switch:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .profile-picture {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .grade-badge {
            font-size: 0.8em;
            padding: 0.25em 0.5em;
        }
    </style>
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>
                <?php echo APP_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_student.php">
                            <i class="fas fa-plus me-1"></i>Add Student
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_students.php">
                            <i class="fas fa-users me-1"></i>View Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search_student.php">
                            <i class="fas fa-search me-1"></i>Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logs.php">
                            <i class="fas fa-file-alt me-1"></i>Logs
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="theme-switch me-2" href="?theme=<?php echo $current_theme === 'light' ? 'dark' : 'light'; ?>">
                            <i class="fas fa-<?php echo $current_theme === 'light' ? 'moon' : 'sun'; ?>"></i>
                            <?php echo ucfirst($current_theme); ?> Mode
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                $message = $_GET['success'];
                switch ($message) {
                    case 'student_added':
                        echo 'Student added successfully!';
                        break;
                    case 'student_updated':
                        echo 'Student updated successfully!';
                        break;
                    case 'student_deleted':
                        echo 'Student deleted successfully!';
                        break;
                    default:
                        echo 'Operation completed successfully!';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php 
                $message = $_GET['error'];
                switch ($message) {
                    case 'login_required':
                        echo 'Please login to access this page.';
                        break;
                    case 'invalid_credentials':
                        echo 'Invalid username or password.';
                        break;
                    case 'student_not_found':
                        echo 'Student not found.';
                        break;
                    case 'upload_error':
                        echo 'File upload failed.';
                        break;
                    default:
                        echo 'An error occurred. Please try again.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?> 