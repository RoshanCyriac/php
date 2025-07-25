<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$search_results = [];
$search_performed = false;
$search_term = '';

// Handle search (GET method)
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = sanitizeInput($_GET['search']);
    $search_results = searchStudents($search_term);
    $search_performed = true;
}

// Get available courses for filtering
$courses = getAvailableCourses();

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2>
            <i class="fas fa-search me-2"></i>Search Students
        </h2>
        <p class="text-muted">Search students by name, email, or course using GET method</p>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Search Criteria
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="" id="searchForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by name, email, or course..." 
                               value="<?php echo htmlspecialchars($search_term); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <a href="search_student.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-refresh me-1"></i>Clear Search
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Search Tips -->
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Search Tips:</strong> You can search by student name, email address, or course name. 
                The search is case-insensitive and will find partial matches.
            </small>
        </div>
    </div>
</div>

<!-- Search Results -->
<?php if ($search_performed): ?>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Search Results
                </h5>
                <span class="badge bg-primary">
                    <?php echo count($search_results); ?> result<?php echo count($search_results) !== 1 ? 's' : ''; ?> found
                </span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($search_results)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No results found</h4>
                    <p class="text-muted">
                        No students found matching "<strong><?php echo htmlspecialchars($search_term); ?></strong>"
                    </p>
                    <div class="mt-3">
                        <a href="add_student.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>Add New Student
                        </a>
                        <a href="view_students.php" class="btn btn-outline-secondary">
                            <i class="fas fa-users me-1"></i>View All Students
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date of Birth</th>
                                <th>Course</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $student): ?>
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
                                    <td>
                                        <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($student['date_of_birth'])); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($student['course']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo getGradeColor($student['grade']); ?> grade-badge">
                                            <?php echo formatGrade($student['grade']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="view_students.php?action=edit&id=<?php echo $student['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="view_students.php?action=delete&id=<?php echo $student['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" title="Delete"
                                               onclick="return confirmDelete('Are you sure you want to delete <?php echo htmlspecialchars($student['name']); ?>?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Quick Search Suggestions -->
<?php if (!$search_performed): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Quick Search Examples
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="?search=computer" class="list-group-item list-group-item-action">
                            <i class="fas fa-search me-2"></i>Search for "computer" (finds Computer Science students)
                        </a>
                        <a href="?search=john" class="list-group-item list-group-item-action">
                            <i class="fas fa-search me-2"></i>Search for "john" (finds students named John)
                        </a>
                        <a href="?search=@email.com" class="list-group-item list-group-item-action">
                            <i class="fas fa-search me-2"></i>Search for "@email.com" (finds all email addresses)
                        </a>
                        <a href="?search=physics" class="list-group-item list-group-item-action">
                            <i class="fas fa-search me-2"></i>Search for "physics" (finds Physics students)
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Search Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <?php
                    $all_students = getAllStudents();
                    $stats = getStudentStatistics();
                    ?>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary"><?php echo $stats['total_students']; ?></h4>
                            <small class="text-muted">Total Students</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?php echo $stats['total_courses']; ?></h4>
                            <small class="text-muted">Active Courses</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-info"><?php echo number_format($stats['average_grade'], 1); ?>%</h5>
                            <small class="text-muted">Average Grade</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-warning"><?php echo number_format($stats['max_grade'], 1); ?>%</h5>
                            <small class="text-muted">Highest Grade</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Distribution -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-book me-2"></i>Students by Course
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $course_counts = [];
                foreach ($all_students as $student) {
                    $course = $student['course'];
                    $course_counts[$course] = isset($course_counts[$course]) ? $course_counts[$course] + 1 : 1;
                }
                ?>
                <?php foreach ($course_counts as $course => $count): ?>
                    <div class="col-md-3 mb-2">
                        <a href="?search=<?php echo urlencode($course); ?>" class="text-decoration-none">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title"><?php echo htmlspecialchars($course); ?></h6>
                                    <span class="badge bg-primary"><?php echo $count; ?> student<?php echo $count !== 1 ? 's' : ''; ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.focus();
    }
});

// Search form validation
document.getElementById('searchForm').addEventListener('submit', function(e) {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput.value.trim()) {
        e.preventDefault();
        searchInput.focus();
        alert('Please enter a search term');
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 