<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (validateUser($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        header('Location: index.php?error=invalid_credentials');
        exit();
    }
}

// If user is not logged in, show login form
if (!isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - <?php echo APP_NAME; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }
            .login-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 15px 15px 0 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card login-card">
                        <div class="card-header login-header text-center py-4">
                            <h3 class="mb-0">
                                <i class="fas fa-graduation-cap me-2"></i>
                                <?php echo APP_NAME; ?>
                            </h3>
                            <p class="mb-0 mt-2">Please login to continue</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Invalid username or password.
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user me-1"></i>Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Password
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" name="login" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-1"></i>Login
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Default: admin / admin123
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// User is logged in, show dashboard
require_once 'includes/header.php';

// Get statistics
$stats = getStudentStatistics();
$students = getAllStudents();
$recent_students = array_slice($students, 0, 5); // Get 5 most recent students
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-4 text-center mb-4">
            <i class="fas fa-graduation-cap me-3"></i>
            Welcome to <?php echo APP_NAME; ?>
        </h1>
        <p class="lead text-center text-muted">
            Manage student records efficiently with our comprehensive system
        </p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h3 class="card-title"><?php echo $stats['total_students']; ?></h3>
                <p class="card-text">Total Students</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-3x mb-3"></i>
                <h3 class="card-title"><?php echo number_format($stats['average_grade'], 1); ?>%</h3>
                <p class="card-text">Average Grade</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x mb-3"></i>
                <h3 class="card-title"><?php echo $stats['total_courses']; ?></h3>
                <p class="card-text">Active Courses</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-star fa-3x mb-3"></i>
                <h3 class="card-title"><?php echo number_format($stats['max_grade'], 1); ?>%</h3>
                <p class="card-text">Highest Grade</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="add_student.php" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Student
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="view_students.php" class="btn btn-info w-100">
                            <i class="fas fa-users me-2"></i>View All Students
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="search_student.php" class="btn btn-success w-100">
                            <i class="fas fa-search me-2"></i>Search Students
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="logs.php" class="btn btn-warning w-100">
                            <i class="fas fa-file-alt me-2"></i>View Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Students -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Recent Students
                </h5>
                <a href="view_students.php" class="btn btn-sm btn-outline-primary">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_students)): ?>
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                        No students found. <a href="add_student.php">Add your first student</a>!
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_students as $student): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($student['profile_picture'])): ?>
                                                <img src="uploads/<?php echo $student['profile_picture']; ?>" 
                                                     alt="Profile" class="profile-picture">
                                            <?php else: ?>
                                                <div class="profile-picture bg-secondary d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['course']); ?></td>
                                        <td>
                                            <span class="badge <?php echo getGradeColor($student['grade']); ?> grade-badge">
                                                <?php echo formatGrade($student['grade']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_students.php?action=edit&id=<?php echo $student['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="view_students.php?action=delete&id=<?php echo $student['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirmDelete('Are you sure you want to delete this student?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 